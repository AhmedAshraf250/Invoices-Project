<?php

test('the application returns a successful response', function () {
    $response = $this->followingRedirects()->get('/index');

    $response->assertOk();
});
