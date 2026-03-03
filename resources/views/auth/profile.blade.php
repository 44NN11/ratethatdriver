@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">My Profile</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Personal Information</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-700"><strong>Name:</strong> <span id="userName"></span></p>
                    <p class="text-gray-700"><strong>Email:</strong> <span id="userEmail"></span></p>
                    <p class="text-gray-700"><strong>Member Since:</strong> <span id="userSince"></span></p>
                </div>
            </div>
            
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Account Actions</h2>
                <div class="space-y-3">
                    <button onclick="showChangePasswordForm()" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                        Change Password
                    </button>
                    <form action="/logout" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">My Recent Ratings</h2>
            <div id="userRatings" class="space-y-3">
                <!-- Ratings will be loaded here -->
            </div>
            <div id="noRatings" class="text-gray-500 text-center py-4 hidden">
                You haven't rated any cars yet.
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadUserProfile();
    loadUserRatings();
});

function loadUserProfile() {
    const user = JSON.parse(localStorage.getItem('user'));
    if (user) {
        document.getElementById('userName').textContent = user.name;
        document.getElementById('userEmail').textContent = user.email;
        document.getElementById('userSince').textContent = new Date(user.created_at).toLocaleDateString();
    }
}

async function loadUserRatings() {
    const user = JSON.parse(localStorage.getItem('user'));
    const ratingsDiv = document.getElementById('userRatings');
    const noRatingsDiv = document.getElementById('noRatings');
    
    if (!user) {
        ratingsDiv.innerHTML = '<p class="text-red-600">Please log in to view your ratings.</p>';
        return;
    }
    
    try {
        // For now, we'll show a message since we don't have a specific API endpoint for user ratings
        // In a real application, you'd have an endpoint like /api/users/{id}/ratings
        ratingsDiv.innerHTML = `
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-gray-600">To view your ratings, we would need to create a specific API endpoint.</p>
                <p class="text-sm text-gray-500 mt-2">This would require adding a route and controller method to fetch ratings by user ID.</p>
            </div>
        `;
        noRatingsDiv.classList.add('hidden');
    } catch (error) {
        console.error('Error loading ratings:', error);
        ratingsDiv.innerHTML = '<p class="text-red-600">Error loading ratings. Please try again later.</p>';
    }
}

function showChangePasswordForm() {
    const ratingsDiv = document.getElementById('userRatings');
    ratingsDiv.innerHTML = `
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Change Password</h3>
            <form id="changePasswordForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                    <input type="password" name="current_password" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="new_password" required minlength="8" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" required minlength="8" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                        Update Password
                    </button>
                    <button type="button" onclick="loadUserRatings()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                </div>
            </form>
            <div id="passwordMessage" class="hidden p-3 rounded-md mt-4"></div>
        </div>
    `;
    
    document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {
            current_password: formData.get('current_password'),
            new_password: formData.get('new_password'),
            new_password_confirmation: formData.get('new_password_confirmation')
        };
        
        const messageDiv = document.getElementById('passwordMessage');
        
        try {
            const response = await fetch('/api/change-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('token') // If using tokens
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (response.ok) {
                messageDiv.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded';
                messageDiv.textContent = 'Password updated successfully!';
                messageDiv.classList.remove('hidden');
                
                // Reset form
                this.reset();
            } else {
                messageDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded';
                messageDiv.textContent = result.message || 'Failed to update password';
                messageDiv.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Password update error:', error);
            messageDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded';
            messageDiv.textContent = 'An error occurred. Please try again.';
            messageDiv.classList.remove('hidden');
        }
    });
}
</script>
@endsection
