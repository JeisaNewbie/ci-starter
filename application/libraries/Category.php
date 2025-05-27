<?php
class Category
{
    const GAME = 'GAME';
    const MOVIE = 'MOVIE';
    const MUSIC = 'MUSIC';
    const SPORTS = 'SPORTS';
    const TALK = 'TALK';

    public static function values() {
        return [
            self::GAME,
            self::MOVIE,
            self::MUSIC,
            self::SPORTS,
            self::TALK
        ];
    }
}