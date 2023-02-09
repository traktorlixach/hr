<?php

namespace Gateway;

use PDO;

class User
{
    /**
     * @var PDO
     */
    private static $instance;

    /**
     * Singleton implementation
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = 'mysql:dbname=db;host=127.0.0.1';
            $user = 'dbuser';
            $password = 'dbpass';
            self::$instance = new PDO($dsn, $user, $password);
        }

        return self::$instance;
    }

    /**
     * Returns a list of users older than the specified age.
     * @param int $ageFrom
     * @return array
     */
    public static function getUsers(int $ageFrom): array
    {
        $stmt = self::getInstance()->prepare("SELECT id, name, lastName, from_date, age, settings FROM Users WHERE age > :ageFrom LIMIT ".\Manager\User::limit);
        $stmt->bindValue(':ageFrom', $ageFrom, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        foreach ($rows as $row) {
            $settings = json_decode($row['settings'], true);
            $users[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'lastName' => $row['lastName'],
                'from_date' => $row['from_date'],
                'age' => $row['age'],
                'key' => $settings['key'],
            ];
        }

        return $users;
    }

    /**
     * Returns a user by name.
     * @param string $name
     * @return array
     */
    public static function getUserByName(string $name): array
    {
        $stmt = self::getInstance()->prepare("SELECT id, name, lastName, from_date, age, settings FROM Users WHERE name = :name");
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        $user_by_name = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'id' => $user_by_name['id'],
            'name' => $user_by_name['name'],
            'lastName' => $user_by_name['lastName'],
            'from_date' => $user_by_name['from_date'],
            'age' => $user_by_name['age'],
        ];
    }

    /**
     * Adds a user to the database.
     * @param string $name
     * @param string $lastName
     * @param int    $age
     * @return string
     */
    public static function add(string $name, string $lastName, int $age): string
    {
        $sth = self::getInstance()->prepare("INSERT INTO Users (name, lastName, age) VALUES (:name, :age, :lastName)");
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':lastName', $lastName, PDO::PARAM_STR);
        $stmt->bindValue(':age', $age, PDO::PARAM_INT);
        $sth->execute();

        return self::getInstance()->lastInsertId();
    }
}
