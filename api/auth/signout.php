<?php
function signoutUser()
{
    session_start();
    session_destroy();
    respond(['message' => 'Logged out']);
}