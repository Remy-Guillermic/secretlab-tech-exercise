<?php

use App\Models\Version;

/** @see VersionControlController::store() */
test('Store version as guest', function () {

    $testKey = fake()->word;
    $testValue = fake()->word;

    $response = $this->json(
        method: 'POST',
        uri: route('object.store'),
        data: [$testKey => $testValue]
    );

    $response->assertCreated();

    $this->assertDatabaseHas('versions', ['key' => $testKey, 'value' => $testValue]);
});

/** @see VersionControlController::index() */
test('Show all records as guest', function () {

    $versionCount = rand(1, 10);

    Version::factory()->count($versionCount)->create();

    $response = $this->json(method: 'GET', uri: route('object.get_all_records'));

    $response->assertOk();

    $response->assertJsonCount($versionCount);
});

/** @see VersionControlController::show() */
test('Show one records as guest without timestamp', function () {

    $version = Version::factory()->create();

    $response = $this->json(
        method: 'GET',
        uri: route('object.show', $version->key),
    );

    $response->assertOk();

    $response->assertJson([$version->key => $version->value]);
});

/** @see VersionControlController::show() */
test('Show one records as guest with timestamp', function (int $timeDiff, ?bool $oldVersion) {

    $now = now('UTC');

    $commonKey = fake()->word;
    $olderTimestamp = $now->copy()->subDays(21);

    $hiddenVersion = Version::factory(['key' => $commonKey])->create();
    $searchedVersion = Version::factory(['key' => $commonKey, 'timestamp' => $olderTimestamp])->create();

    $response = $this->json(
        method: 'GET',
        uri: route('object.show', [
            'key' => $commonKey,
            'timestamp' => $now->addDays($timeDiff)->unix(),
        ]),
    );

    $response->assertOk();

    match ($oldVersion) {
        true => $response
            ->assertJson([$searchedVersion->key => $searchedVersion->value])
            ->assertJsonMissing([$hiddenVersion->key => $hiddenVersion->value]),
        false => $response
            ->assertJson([$hiddenVersion->key => $hiddenVersion->value])
            ->assertJsonMissing([$searchedVersion->key => $searchedVersion->value]),
        default => $response->assertSee($oldVersion),
    };

})->with([
    'inferior timestamp' => [-42, null],
    'equal timestamp' => [-21, true],
    'superior timestamp' => [42, false],
]);


/** @see VersionControlController */

test('Test exercise workflow', function () {

    $testKey = fake()->word;
    $testValue1 = fake()->word;
    $testValue2 = fake()->word;

    $this->assertDatabaseEmpty('versions');
    $this->json(method: 'POST', uri: route('object.store'), data: [$testKey => $testValue1])
        ->assertStatus(201);
    $this->assertDatabaseHas('versions', ['key' => $testKey, 'value' => $testValue1]);

    $this->json(method: 'GET', uri: route('object.show', ['key' => $testKey]))
        ->assertOk()
        ->assertJsonFragment([$testKey => $testValue1]);

    $this->json(method: 'POST', uri: route('object.store'), data: [$testKey => $testValue2])
        ->assertStatus(201);
    $this->assertDatabaseCount('versions', 2);
    $this->assertDatabaseHas('versions', ['key' => $testKey, 'value' => $testValue2]);

    $this->json(method: 'GET', uri: route('object.show', ['key' => $testKey]))
        ->assertOk()
        ->assertJsonFragment([$testKey => $testValue2]);

    $updatedTimestamp = now('UTC')->subDay();

    Version::unguard();

    Version::where(['key' => $testKey, 'value' => $testValue1])->update(['timestamp' => $updatedTimestamp]);

    Version::reguard();

    $this
        ->json(
            method: 'GET',
            uri: route('object.show', [
                'key' => $testKey,
                'timestamp' => $updatedTimestamp->addHour()->unix(),
            ]),
        )->assertOk()
        ->assertJsonFragment([$testKey => $testValue1]);

    $this->json(method: 'GET', uri: route('object.get_all_records'))
        ->assertOk()
        ->assertJsonFragment([$testKey => [$testValue1, $testValue2]]);
});


