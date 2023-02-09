<?php

namespace Manager;

class User
{
    const LIMIT = 10;

    /**
     * Get users who are older than a specified age
     *
     * @param int $ageFrom Minimum age
     * @return array Array of users
     */
    public function getUsers(int $ageFrom): array
    {
        return \Gateway\User::getUsers($ageFrom);
    }

    /**
     * Get users by a list of names
     *
     * @return array Array of users
     */
    public static function getByNames(): array
    {
        if (!isset($_GET['names']) || !is_array($_GET['names'])) {
            return [];
        }

        $users = [];
        foreach ($_GET['names'] as $name) {
            $users[] = \Gateway\User::user($name);
        }

        return $users;
    }

    /**
     * Add users to the database
     *
     * @param array $users Array of users to add
     * @return array Array of user IDs
     */
    public function addUsers(array $users): array
    {
        $ids = [];
        \Gateway\User::getInstance()->beginTransaction();
        foreach ($users as $user) {
            try {
                \Gateway\User::add($user['name'], $user['lastName'], $user['age']);
                \Gateway\User::getInstance()->commit();
                $ids[] = \Gateway\User::getInstance()->lastInsertId();
            } catch (\Exception $e) {
                \Gateway\User::getInstance()->rollBack();
            }
        }

        return $ids;
    }
}
