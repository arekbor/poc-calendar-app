<?php

declare(strict_types=1);

namespace App\Repository;

final class CalendarEventRepository extends AbstractRepository
{
    public function getCalendarEventsInRange(array $range): array
    {   
        $sql = 'SELECT * FROM calendar_events WHERE start_date <= (:end_date) AND end_date >= (:start_date)';

        return $this->fetchAll($sql, $range);
    }

    public function update(array $calendarEvent): void
    {
        $sql = '
            UPDATE calendar_events
            SET title = (:title), start_date = (:start_date), end_date = (:end_date), color = (:color)
            WHERE id = (:id)
        ';

        $this->execute($sql, $calendarEvent);
    }

    public function create(array $calendarEvent): int
    {
        $sql = '
            INSERT INTO calendar_events (title, start_date, end_date, color) 
            VALUES 
            (:title, :start_date, :end_date, :color)
        ';

        $this->execute($sql, $calendarEvent);

        $lastInsertId = $this->pdo->lastInsertId('calendar_events_id_seq');
        return intval($lastInsertId);
    }       

    public function delete(int $id): void
    {
        $sql = 'DELETE FROM calendar_events WHERE id = (:id)';

        $this->execute($sql, ['id' => $id]);
    }

    public function get(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM calendar_events WHERE id = (:id)', [
            'id' => $id
        ]);
    }
}