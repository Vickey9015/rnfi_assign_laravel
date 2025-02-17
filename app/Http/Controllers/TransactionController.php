<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // Store a new transaction
    public function store(Request $request)
    {


        // Validate incoming request
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);


        $user = $request->user();

        // Create a new transaction
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'content' => $request->content,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Transaction created successfully',
            'transaction' => $transaction,
        ], 201);
    }


    // Retrieve a list of transactions for the logged-in user
    public function showTransactions(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json(['error' => 'Unauthorized. Token may be invalid or expired.'], 401);
        }

        // Get all transactions for the authenticated user
        $transactions = $user->transactions;

        return response()->json([
            'transactions' => $transactions,
        ], 200);
    }

    public function showTransactionByID(Request $request, $id)
    {
        $user = $request->user(); // Get the authenticated user
       

        if (!$user) {
            return response()->json(['error' => 'Unauthorized. Token may be invalid or expired.'], 401);
        }

        // Retrieve the transaction by ID and ensure it belongs to the authenticated user
        $transaction = Transaction::where('user_id', $user->id)->find($id);

        // If the transaction is not found, return a 404 error
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found or unauthorized.'], 404);
        }

        return response()->json([
            'transaction' => $transaction,
        ], 200);
    }
}
