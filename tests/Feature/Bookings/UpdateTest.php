<?php

namespace Tests\Feature\Bookings;

use Hydrofon\Booking;
use Hydrofon\Object;
use Hydrofon\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Bookings can be updated.
     *
     * @return void
     */
    public function testBookingsCanBeUpdated()
    {
        $user      = factory(User::class)->create();
        $booking   = factory(Booking::class)->create();
        $newObject = factory(Object::class)->create();

        $response = $this->actingAs($user)->put('bookings/' . $booking->id, [
            'object_id'  => $newObject->id,
            'start_time' => $booking->start_time,
            'end_time'   => $booking->end_time,
        ]);

        $response->assertRedirect('/bookings');
        $this->assertDatabaseHas('bookings', [
            'object_id' => $newObject->id,
        ]);
    }

    /**
     * Bookings must have an object.
     *
     * @return void
     */
    public function testBookingsMustHaveAnObject()
    {
        $user    = factory(User::class)->create();
        $booking = factory(Booking::class)->create();

        $response = $this->actingAs($user)->put('bookings/' . $booking->id, [
            'object_id'  => '',
            'start_time' => $booking->start_time,
            'end_time'   => $booking->end_time,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'object_id' => $booking->object_id,
        ]);
    }

    /**
     * The requested object must exist in the database.
     *
     * @return void
     */
    public function testObjectMustExist()
    {
        $user    = factory(User::class)->create();
        $booking = factory(Booking::class)->create();

        $response = $this->actingAs($user)->put('bookings/' . $booking->id, [
            'object_id'  => 100,
            'start_time' => $booking->start_time,
            'end_time'   => $booking->end_time,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'object_id' => $booking->object_id,
        ]);
    }
}
