<?php

namespace Caramel\Model;

class Post extends Model
{

    protected static $table = 'posts';

    public string $title;
    public int $user_id;
    public string $body;
    public string $created_at;

}