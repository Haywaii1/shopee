<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Order extends Model
{
    use HasFactory;

    // Define the table name if it's different from the model name
    protected $table = 'orders';

    // Specify the mass assignable attributes
    protected $fillable = [
        'user_id',
        'products',
        'total_price',
    ];

    // Cast the products field to an array (since it's stored as JSON in the database)
    protected $casts = [
        'products' => 'array',
    ];

    // Define the relationship with the User model (assuming an Order belongs to a User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

