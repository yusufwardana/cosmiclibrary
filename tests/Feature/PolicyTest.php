<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BorrowRecord;
use App\Models\Category;
use App\Models\Fine;
use App\Models\Member;
use App\Models\Permission;
use App\Models\Reservation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    private function userWithPermission(string $slug): User
    {
        $permission = Permission::factory()->create(['slug' => $slug]);
        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    public function test_book_policy_allows_and_denies(): void
    {
        $user = $this->userWithPermission('book.view');
        $book = Book::factory()->create();

        $this->assertTrue($user->can('view', $book));
        $this->assertTrue($user->can('viewAny', Book::class));

        $noPerm = User::factory()->create();
        $this->assertFalse($noPerm->can('view', $book));
        $this->assertFalse($noPerm->can('viewAny', Book::class));
    }

    public function test_book_policy_create_update_delete(): void
    {
        $this->assertTrue($this->userWithPermission('book.create')->can('create', Book::class));

        $userUpdate = $this->userWithPermission('book.update');
        $book = Book::factory()->create();
        $this->assertTrue($userUpdate->can('update', $book));

        $userDelete = $this->userWithPermission('book.delete');
        $this->assertTrue($userDelete->can('delete', $book));
    }

    public function test_category_policy(): void
    {
        $user = $this->userWithPermission('book.view');
        $category = Category::factory()->create();

        $this->assertTrue($user->can('view', $category));
        $this->assertTrue($user->can('viewAny', Category::class));

        $noPerm = User::factory()->create();
        $this->assertFalse($noPerm->can('view', $category));
    }

    public function test_member_policy(): void
    {
        $user = $this->userWithPermission('member.view');
        $member = Member::factory()->create();

        $this->assertTrue($user->can('view', $member));
        $this->assertTrue($user->can('viewAny', Member::class));

        $noPerm = User::factory()->create();
        $this->assertFalse($noPerm->can('view', $member));
    }

    public function test_borrow_record_policy(): void
    {
        $user = $this->userWithPermission('borrow.view');
        $record = BorrowRecord::factory()->create();

        $this->assertTrue($user->can('view', $record));
        $this->assertTrue($user->can('viewAny', BorrowRecord::class));

        $noPerm = User::factory()->create();
        $this->assertFalse($noPerm->can('view', $record));
    }

    public function test_borrow_record_policy_return_extend(): void
    {
        $userReturn = $this->userWithPermission('borrow.return');
        $userExtend = $this->userWithPermission('borrow.extend');
        $record = BorrowRecord::factory()->create();

        $this->assertTrue($userReturn->can('return', $record));
        $this->assertTrue($userExtend->can('extend', $record));
    }

    public function test_reservation_policy(): void
    {
        $user = $this->userWithPermission('reservation.view');
        $reservation = Reservation::factory()->create();

        $this->assertTrue($user->can('view', $reservation));
        $this->assertTrue($user->can('viewAny', Reservation::class));

        $noPerm = User::factory()->create();
        $this->assertFalse($noPerm->can('view', $reservation));
    }

    public function test_reservation_policy_cancel(): void
    {
        $user = $this->userWithPermission('reservation.cancel');
        $reservation = Reservation::factory()->create();

        $this->assertTrue($user->can('cancel', $reservation));
    }

    public function test_fine_policy(): void
    {
        $user = $this->userWithPermission('fine.view');
        $fine = Fine::factory()->create();

        $this->assertTrue($user->can('view', $fine));
        $this->assertTrue($user->can('viewAny', Fine::class));

        $noPerm = User::factory()->create();
        $this->assertFalse($noPerm->can('view', $fine));
    }

    public function test_fine_policy_pay_waive(): void
    {
        $userPay = $this->userWithPermission('fine.pay');
        $userWaive = $this->userWithPermission('fine.waive');
        $fine = Fine::factory()->create();

        $this->assertTrue($userPay->can('pay', $fine));
        $this->assertTrue($userWaive->can('waive', $fine));
    }
}
