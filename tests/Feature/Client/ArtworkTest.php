<?php

test('user can access Artwork index', function () {
    $response = $this
        ->getJson(route('client.artwork.index'));

    $response->assertOk();
    $response->assertJsonStructure([
        'data'
    ]);
});
