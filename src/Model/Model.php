<?php

namespace Caramel\Model;

use Caramel\Core\App;
use Caramel\Core\Database;

class Model
{

    protected static Database $db;
    protected static $table;

    public int $id;
    protected array $fields = [];

    public function __construct()
    {
        $this->db = app()->container()->resolve(Database::class);
    }

    private function fields($with_id = false)
    {
        $values = [];
        foreach($this->fields as $field)
        {
            $values[$field] = $this->{$field};
        }

        if($with_id)
        {
            $values['id'] = $this->id;
        }
        return $values;
    }

    public function save()
    {   

        // --- updating existing model
        if(isset($this->id))
        {
            $updated_fields = [];

            $updated_fields_values = $this->fields(true);
            foreach($this->fields as $field)
            {
                $updated_fields[] = "`$field` = :$field";
            }
            $this->db->execute(sprintf("UPDATE `%s` SET %s WHERE `id` = :id", static::$table, join(', ', $updated_fields)), $updated_fields_values);
        }
        // --- store new model
        {   
            $fields_list =  join(', ', $this->fields);
            $named_value_list =  join(', ', array_map(fn($value) => ":$value", $this->fields));
            $this->db->execute(sprintf("INSERT INTO `%s` (%s) VALUES (%s)", static::$table, $fields_list, $named_value_list), $this->fields());
            $this->id = $this->db->lastInsertId();
        } 
    }


    public static function create(array $data): static
    {
        $model = new static();
        foreach($model->fields as $field)
        {
            $model->{$field} = $data[$field];
        }
        return $model;
    }

    public static function store(array $data): static
    {
        $model = static::create($data);
        $model->save();
        return $model;
    }

    public static function all()
    {
        $db = app()->container()->resolve(Database::class);

        $models = [];

        if($result = $db->query(
            sprintf("SELECT * FROM `%s`", static::$table),
        ))
        {
            foreach($result as $entry)
            {
                $model = new static();
                foreach($entry as $key => $value)
                {
                    if(property_exists($model, $key))
                    {
                        $model->{$key} = $value;    
                    }
                    else
                    {
                        throw new \Exception(sprintf("Attempted to set '%s' as property on model but the model has no such property.", $key));
                    } 
                }
                $models[] = $model;
            }
        }


        return $models;
    }

    public static function find(int $id): ?static
    {

        $db = app()->container()->resolve(Database::class);

        if($result = $db->queryOne(
            sprintf("SELECT * FROM `%s` WHERE `id` = :id LIMIT 1", static::$table),
            [
                'id' => $id
            ]            
        ))
        {
            $model = new static();
            foreach($result as $key => $value)
            {
                if(property_exists($model, $key))
                {
                    $model->{$key} = $value;    
                }
                else
                {
                    throw new \Exception(sprintf("Attempted to set '%s' as property on model but the model has no such property.", $key));
                } 
            }
            return $model;
        }


        return null;
    }

    public static function where(string $key, string $value, string $operator): array
    {
        $db = app()->container()->resolve(Database::class);
        $models = [];

        if($results = $db->query(
            sprintf("SELECT * FROM `%s` WHERE `%s` %s :%s", static::$table, $key, $operator, $key),
            [
                $key => $value
            ]            
        ))
        {
            foreach($results as $result)
            {
                $model = new static();
                foreach($result as $key => $value)
                {
                    if(property_exists($model, $key))
                    {
                        $model->{$key} = $value;    
                    }
                    else
                    {
                        throw new \Exception(sprintf("Attempted to set '%s' as property on model but the model has no such property.", $key));
                    } 
                }
                $models[] = $model;
            }
        }


        return $models;
    }

    public static function whereFirst(string $key, string $value, string $operator): ?static
    {
        $db = app()->container()->resolve(Database::class);

        if($result = $db->queryOne(
            sprintf("SELECT * FROM `%s` WHERE `%s` %s :%s LIMIT 1", static::$table, $key, $operator, $key),
            [
                $key => $value
            ]            
        ))
        {
            $model = new static();
            foreach($result as $key => $value)
            {
                if(property_exists($model, $key))
                {
                    $model->{$key} = $value;    
                }
                else
                {
                    throw new \Exception(sprintf("Attempted to set '%s' as property on model but the model has no such property.", $key));
                } 
            }
            return $model;
        }


        return null;
    }


}