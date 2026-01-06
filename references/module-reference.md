## DTOs
class CreateCartDTO
{
    public function __construct(
        public string $userId,
        /** @var array<int, array{book_id:string, quantity:int, price_cents_at_add:int}> */
        public array $items
    ) {}
}

class UpdateCartItemDTO
{
    public function __construct(
        public string $itemId,
        public string $userId,
        public int $quantity
    ) {}
}

## Model
class Cart extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'carts';

    /**
     * Primary key menggunakan UUID string
     */
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
    ];

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
    ];

    /**
     * Relasi ke user (1 cart milik 1 user)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi cart → cart_items
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}

## Repository
interface CartRepositoryInterface
{
    public function findByUser(string $userId): ?Cart;
    public function getCartCount(string $userId): int;
    public function getCartDetail(string $userId): ?array;
    public function createOrReplace(string $userId, array $items): array;
    public function updateItemQuantity(string $itemId, string $userId, int $qty): array;
    public function removeItem(string $itemId, string $userId): array;
    public function decrementItem(string $itemId, string $userId): array;
}

class CartRepository implements CartRepositoryInterface
{
    public function findByUser(string $userId): ?Cart
    {
        return Cart::where('user_id', $userId)->first();
    }

    public function getCartCount(string $userId): int
    {
        $cart = $this->findByUser($userId);
        return $cart ? $cart->items()->count() : 0;
    }

    public function getCartDetail(string $userId): ?array
    {
        $cart = Cart::where('user_id', $userId)->first();
        if (! $cart) return null;

        $items = CartItem::query()
            ->where('cart_id', $cart->id)
            ->join('books', 'books.id', '=', 'cart_items.book_id')
            ->leftJoin('authors', 'authors.id', '=', 'books.author_id')
            ->orderBy('cart_items.created_at')
            ->get([
                'cart_items.*',
                'books.title as book_title',
                'books.cover_url',
                'authors.name as author_name',
                'books.category_id',
            ]);

        return [
            'id' => $cart->id,
            'user_id' => $cart->user_id,
            'updated_at' => $cart->updated_at,
            'items' => $items,
        ];
    }

    public function createOrReplace(string $userId, array $items): array
    {
        return DB::transaction(function () use ($userId, $items) {
            $cart = Cart::firstOrCreate(
                ['user_id' => $userId],
            );

            CartItem::where('cart_id', $cart->id)->delete();

            foreach ($items as $item) {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'book_id' => $item['book_id'],
                    'quantity' => $item['quantity'],
                    'price_cents_at_add' => $item['price_cents_at_add'],
                ]);
            }

            $cart->touch();

            return $this->getCartDetail($userId);
        });
    }

    public function updateItemQuantity(string $itemId, string $userId, int $qty): array
    {
        if ($qty < 1) {
            abort(400, 'Quantity must be at least 1');
        }

        $item = CartItem::with('cart', 'book')->findOrFail($itemId);

        if ($item->cart->user_id !== $userId) {
            abort(403, 'Forbidden');
        }

        if ($item->book && $qty > $item->book->stock) {
            abort(400, 'Quantity exceeds stock');
        }

        $item->update(['quantity' => $qty]);
        $item->cart->touch();

        return $this->getCartDetail($userId);
    }

    public function removeItem(string $itemId, string $userId): array
    {
        $item = CartItem::with('cart')->findOrFail($itemId);

        if ($item->cart->user_id !== $userId) {
            abort(403, 'Forbidden');
        }

        $item->delete();
        $item->cart->touch();

        return $this->getCartDetail($userId);
    }

    public function decrementItem(string $itemId, string $userId): array
    {
        $item = CartItem::with('cart')->findOrFail($itemId);

        if ($item->cart->user_id !== $userId) {
            abort(403, 'Forbidden');
        }

        if ($item->quantity > 1) {
            $item->decrement('quantity');
        } else {
            $item->delete();
        }

        $item->cart->touch();

        return $this->getCartDetail($userId);
    }
}

## Service
class CartService
{
    public function __construct(
        protected CartRepositoryInterface $repo
    ) {}

    public function count(string $userId): int
    {
        return $this->repo->getCartCount($userId);
    }

    public function detail(string $userId): ?array
    {
        return $this->repo->getCartDetail($userId);
    }

    public function create(CreateCartDTO $dto): array
    {
        return $this->repo->createOrReplace($dto->userId, $dto->items);
    }

    public function updateItem(string $itemId, string $userId, int $qty): array
    {
        return $this->repo->updateItemQuantity($itemId, $userId, $qty);
    }

    public function removeItem(string $itemId, string $userId): array
    {
        return $this->repo->removeItem($itemId, $userId);
    }

    public function decrement(string $itemId, string $userId): array
    {
        return $this->repo->decrementItem($itemId, $userId);
    }
}

## Request
namespace App\Http\Requests\Api\V1;

## Controller
class CartController extends Controller
{
    public function __construct(
        protected CartService $service
    ) {}

    public function count(Request $request)
    {
        return response()->json([
            'count' => $this->service->count($request->user()->id),
        ]);
    }

    public function show(Request $request)
    {
        return response()->json(
            $this->service->detail($request->user()->id)
        );
    }

    public function store(Request $request)
    {
        $dto = new CreateCartDTO(
            $request->user()->id,
            $request->validate([
                'items' => 'array',
                'items.*.book_id' => 'required|uuid',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price_cents_at_add' => 'required|integer|min:0',
            ])['items'] ?? []
        );

        return response()->json($this->service->create($dto));
    }

    public function updateItem(Request $request, string $itemId)
    {
        return response()->json(
            $this->service->updateItem(
                $itemId,
                $request->user()->id,
                $request->integer('quantity')
            )
        );
    }

    public function destroyItem(Request $request, string $itemId)
    {
        return response()->json(
            $this->service->removeItem($itemId, $request->user()->id)
        );
    }

    public function decrement(Request $request, string $itemId)
    {
        return response()->json(
            $this->service->decrement($itemId, $request->user()->id)
        );
    }
}

## Factory
class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        return [
            'id' => (string) fake()->uuid(),
            'user_id' => User::factory(),
        ];
    }
}

## Test
uses(RefreshDatabase::class);

simpan postJson dalam sebuah paragraf misal $postData agar mudah dump() untuk cek error
import use function Pest\Laravel\{postJson, assertDatabaseHas, withHeader};

beforeEach(function () {
    $this->category = Category::create([
        'id'   => fake()->uuid(),
        'name' => 'Novel',
        'slug' => 'novel',
    ]);

    $this->author = Author::create([
        'id'   => fake()->uuid(),
        'name' => 'Tere Liye',
        'bio'  => 'Penulis terkenal Indonesia',
    ]);

    $this->user = User::factory()->create();
});


it('can create and fetch cart', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $book = createBook(['title' => 'Book 1', 'slug' => 'book-1', 'stock' => 20]);

    actingAs($user)
        ->postJson('/api/v1/cart', [
            'items' => [
                [
                    'book_id' => $book->id,
                    'category_id' => $category->id,
                    'quantity' => 2,
                    'price_cents_at_add' => 15000,
                ],
            ],
        ])
        ->assertOk()
        ->assertJsonPath('items.0.quantity', 2);

    getJson('/api/v1/cart')
        ->assertOk()
        ->assertJsonCount(1, 'items');
});

it('prevents updating another user cart item', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $book = createBook();

    $cart = Cart::factory()->for($user)->create();
    $item = CartItem::factory()->for($cart)->create(['book_id' => $book->id,'quantity' => 1]);

    actingAs($other)
        ->patchJson("/api/v1/cart/items/{$item->id}", ['quantity' => 2])
        ->assertForbidden();
});

it('can increment cart item quantity', function () {
    $user = User::factory()->create();
    $book = createBook(['stock' => 10]);

    actingAs($user)->postJson('/api/v1/cart', [
        'items' => [[
            'book_id' => $book->id,
            'quantity' => 1,
            'price_cents_at_add' => 15000,
        ]],
    ]);

    $item = CartItem::first();

    actingAs($user)
        ->patchJson("/api/v1/cart/items/{$item->id}", ['quantity' => 3])
        ->assertOk()
        ->assertJsonPath('items.0.quantity', 3);
});

it('can decrement cart item quantity', function () {
    $user = User::factory()->create();
    $book = createBook();

    actingAs($user)->postJson('/api/v1/cart', [
        'items' => [[
            'book_id' => $book->id,
            'quantity' => 2,
            'price_cents_at_add' => 15000,
        ]],
    ]);

    $item = CartItem::first();

    actingAs($user)
        ->postJson("/api/v1/cart/items/{$item->id}/decrement")
        ->assertOk()
        ->assertJsonPath('items.0.quantity', 1);
});

it('removes item when decrementing from quantity 1', function () {
    $user = User::factory()->create();
    $book = createBook();

    actingAs($user)->postJson('/api/v1/cart', [
        'items' => [[
            'book_id' => $book->id,
            'quantity' => 1,
            'price_cents_at_add' => 15000,
        ]],
    ]);

    $item = CartItem::first();

    actingAs($user)
        ->postJson("/api/v1/cart/items/{$item->id}/decrement")
        ->assertOk()
        ->assertJsonCount(0, 'items');

    expect(CartItem::count())->toBe(0);
});

it('can update cart item quantity directly', function () {
    $user = User::factory()->create();
    $book = createBook(['stock' => 10]);

    actingAs($user)->postJson('/api/v1/cart', [
        'items' => [[
            'book_id' => $book->id,
            'quantity' => 1,
            'price_cents_at_add' => 15000,
        ]],
    ]);

    $item = CartItem::first();

    actingAs($user)
        ->patchJson("/api/v1/cart/items/{$item->id}", ['quantity' => 5])
        ->assertOk()
        ->assertJsonPath('items.0.quantity', 5);
});

it('can remove cart item', function () {
    $user = User::factory()->create();
    $book = createBook();

    actingAs($user)->postJson('/api/v1/cart', [
        'items' => [[
            'book_id' => $book->id,
            'quantity' => 2,
            'price_cents_at_add' => 15000,
        ]],
    ]);

    $item = CartItem::first();

    actingAs($user)
        ->deleteJson("/api/v1/cart/items/{$item->id}")
        ->assertOk()
        ->assertJsonCount(0, 'items');
});

