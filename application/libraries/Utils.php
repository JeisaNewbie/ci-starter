<?php
class Utils
{
    public static function validate_user($user_id, $user_id_to_validate)
    {
        if ($user_id_to_validate !== $user_id)
        {
            return FALSE;
        }
        return TRUE;
    }
}