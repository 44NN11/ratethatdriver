@extends('layouts.app')

@section('title', 'RateThatDriver - Search Cars')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Welcome Message -->
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">RateThatDriver</h1>
        <p class="text-gray-600">Find and rate drivers by car registration number</p>
    </div>

    <!-- Search Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form id="searchForm" class="space-y-4">
            <div class="flex gap-4">
                <div class="flex-1">
                    <label for="registration" class="block text-sm font-medium text-gray-700 mb-1">
                        Car Registration Number
                    </label>
                    <input type="text" 
                           id="registration" 
                           name="registration" 
                           required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., ABC123"
                           style="text-transform: uppercase;">
                </div>
                <div class="flex items-end">
                    <button type="submit" 
                            id="searchBtn"
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Search
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Search Results -->
    <div id="searchResults" class="hidden">
        <!-- Results will be displayed here -->
    </div>

    <!-- Message Area -->
    <div id="message" class="hidden p-3 rounded-md"></div>
</div>

<script>
document.getElementById('searchForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const registration = document.getElementById('registration').value.toUpperCase().trim();
    const searchBtn = document.getElementById('searchBtn');
    const resultsDiv = document.getElementById('searchResults');
    const messageDiv = document.getElementById('message');
    
    // Reset previous results
    resultsDiv.classList.add('hidden');
    messageDiv.classList.add('hidden');
    messageDiv.className = 'hidden p-3 rounded-md';
    
    // Validate registration
    if (!registration) {
        showMessage('Please enter a registration number', 'error');
        return;
    }
    
    // Disable button and show loading
    searchBtn.disabled = true;
    searchBtn.textContent = 'Searching...';
    
    try {
        const response = await fetch(`/api/cars/${registration}`);
        const result = await response.json();
        
        if (response.ok) {
            displayCarResults(result);
        } else {
            showMessage(result.message || 'Car not found', 'error');
        }
    } catch (error) {
        console.error('Search error:', error);
        showMessage('An error occurred. Please try again.', 'error');
    } finally {
        // Re-enable button
        searchBtn.disabled = false;
        searchBtn.textContent = 'Search';
    }
});

function displayCarResults(data) {
    const resultsDiv = document.getElementById('searchResults');
    const car = data.car;
    const ratings = data.ratings;
    
    // Calculate average rating
    const avgRating = ratings.length > 0 
        ? (ratings.reduce((sum, r) => sum + r.rating, 0) / ratings.length).toFixed(1)
        : 0;
    
    resultsDiv.innerHTML = `
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Car: ${car.registration_number}</h2>
                    <p class="text-gray-600">Registered: ${new Date(car.created_at).toLocaleDateString()}</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-blue-600">${avgRating}</div>
                    <div class="text-sm text-gray-600">Average Rating</div>
                </div>
            </div>
            
            <div class="border-t pt-4">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-semibold text-gray-900">Ratings (${ratings.length})</h3>
                    ${window.location.pathname !== '/' ? '' : `
                        <button onclick="showRatingForm('${car.registration_number}')" 
                                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                            Add Rating
                        </button>
                    `}
                </div>
                
                ${ratings.length === 0 ? `
                    <p class="text-gray-500 text-center py-4">No ratings yet. Be the first to rate this car!</p>
                ` : `
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        ${ratings.map(rating => `
                            <div class="border rounded-lg p-3">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Rating: ${rating.rating}/5
                                        </span>
                                        ${rating.location ? `
                                            <span class="ml-2 text-sm text-gray-600">📍 ${rating.location}</span>
                                        ` : ''}
                                    </div>
                                    <span class="text-xs text-gray-500">${new Date(rating.created_at).toLocaleDateString()}</span>
                                </div>
                                ${rating.comment ? `
                                    <p class="text-gray-700">${rating.comment}</p>
                                ` : ''}
                            </div>
                        `).join('')}
                    </div>
                `}
            </div>
        </div>
    `;
    
    resultsDiv.classList.remove('hidden');
}

function showRatingForm(registration) {
    const resultsDiv = document.getElementById('searchResults');
    const car = { registration_number: registration };
    
    resultsDiv.innerHTML = `
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Add Rating for ${car.registration_number}</h2>
            
            <form id="ratingForm" class="space-y-4">
                <input type="hidden" id="ratingRegistration" value="${car.registration_number}">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                    <div class="flex gap-2">
                        ${[1,2,3,4,5].map(star => `
                            <button type="button" 
                                    onclick="selectRating(${star})"
                                    class="rating-star w-12 h-12 rounded-full border-2 border-gray-300 hover:border-yellow-400 hover:bg-yellow-50 transition-colors"
                                    data-rating="${star}">
                                ⭐
                            </button>
                        `).join('')}
                    </div>
                    <input type="hidden" id="selectedRating" name="rating" required>
                    <div id="ratingError" class="text-red-600 text-sm mt-1 hidden"></div>
                </div>
                
                <div>
                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Comment (Optional)</label>
                    <textarea id="comment" name="comment" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Share your experience with this driver..."></textarea>
                </div>
                
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location (Optional)</label>
                    <input type="text" id="location" name="location" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Where did you encounter this car?">
                </div>
                
                <div class="flex gap-3">
                    <button type="submit" id="submitRatingBtn" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                        Submit Rating
                    </button>
                    <button type="button" onclick="displayCarResults(${JSON.stringify({car, ratings: []})})" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                </div>
            </form>
            
            <div id="ratingMessage" class="hidden p-3 rounded-md mt-4"></div>
        </div>
    `;
    
    resultsDiv.classList.remove('hidden');
}

function selectRating(rating) {
    // Reset all stars
    document.querySelectorAll('.rating-star').forEach(star => {
        star.classList.remove('border-yellow-400', 'bg-yellow-50');
        star.classList.add('border-gray-300');
    });
    
    // Highlight selected stars
    for (let i = 1; i <= rating; i++) {
        const star = document.querySelector(`.rating-star[data-rating="${i}"]`);
        star.classList.remove('border-gray-300');
        star.classList.add('border-yellow-400', 'bg-yellow-50');
    }
    
    document.getElementById('selectedRating').value = rating;
    document.getElementById('ratingError').classList.add('hidden');
}

document.addEventListener('submit', async function(e) {
    if (e.target.id === 'ratingForm') {
        e.preventDefault();
        
        const registration = document.getElementById('ratingRegistration').value;
        const rating = document.getElementById('selectedRating').value;
        const comment = document.getElementById('comment').value;
        const location = document.getElementById('location').value;
        
        const submitBtn = document.getElementById('submitRatingBtn');
        const messageDiv = document.getElementById('ratingMessage');
        
        // Validate rating
        if (!rating) {
            document.getElementById('ratingError').textContent = 'Please select a rating';
            document.getElementById('ratingError').classList.remove('hidden');
            return;
        }
        
        // Disable button
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';
        
        try {
            const response = await fetch(`/api/cars/${registration}/ratings`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    rating: parseInt(rating),
                    comment: comment,
                    location: location
                })
            });
            
            const result = await response.json();
            
            if (response.ok) {
                messageDiv.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded';
                messageDiv.textContent = 'Rating submitted successfully!';
                messageDiv.classList.remove('hidden');
                
                // Refresh the car data to show the new rating
                setTimeout(async () => {
                    const response = await fetch(`/api/cars/${registration}`);
                    const result = await response.json();
                    if (response.ok) {
                        displayCarResults(result);
                    }
                }, 1000);
                
            } else {
                messageDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded';
                messageDiv.textContent = result.message || 'Failed to submit rating';
                messageDiv.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Rating error:', error);
            messageDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded';
            messageDiv.textContent = 'An error occurred. Please try again.';
            messageDiv.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Rating';
        }
    }
});

function showMessage(message, type) {
    const messageDiv = document.getElementById('message');
    messageDiv.textContent = message;
    messageDiv.className = type === 'error' 
        ? 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded'
        : 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded';
    messageDiv.classList.remove('hidden');
}
</script>
@endsection
