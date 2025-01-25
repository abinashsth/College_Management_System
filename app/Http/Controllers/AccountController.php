<?php

namespace App\Http\Controllers;


use App\Models\ClassModel; 
use App\Models\Account; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AccountController extends Controller
{
    public function index()
    {
        // Fetch accounts data
        $accounts = Account::paginate(10);

        // Pass the data to the view
        return view('account.index', compact('accounts'));
    }

    public function create()
    {
          // Fetch the list of classes from the database
          $classes = ClassModel::all(); // Replace 'ClassModel' with your actual class model name

          // Pass the classes to the view
          return view('account.create', compact('classes'));

    }


    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Add other fields as needed
        ]);

        // Create the account
        Account::create($validated);

        // Redirect to the index route with success message
        return redirect()->route('account.index')->with('success', 'Account created successfully!');
    }


}
