// EduConnect-RDC Main Application

document.addEventListener('DOMContentLoaded', function() {
    console.log('EduConnect-RDC initialized');
    initializeApp();
});

const APP_BASE_PATH = document.querySelector('meta[name="app-base-path"]')?.content || '';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';

/**
 * Initialize application
 */
function initializeApp() {
    setupEventListeners();
    initializeNotifications();
}

/**
 * Setup event listeners
 */
function setupEventListeners() {
    // Like button handlers
    setupLikeButtons();
    
    // Comment form handlers
    setupCommentHandlers();
    
    // Message form handlers
    setupMessageHandlers();
}

/**
 * Setup like buttons
 */
function setupLikeButtons() {
    document.querySelectorAll('.btn-like-post').forEach(btn => {
        btn.addEventListener('click', handleLike);
    });
}

/**
 * Handle like action
 */
function handleLike(e) {
    e.preventDefault();
    const postId = this.dataset.postId;
    
    fetch(APP_BASE_PATH + '/post/' + postId + '/like', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'post_id=' + postId + '&csrf_token=' + encodeURIComponent(CSRF_TOKEN)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const icon = data.liked ? '❤️' : '🤍';
            this.innerHTML = icon + ' <span>' + data.likes_count + '</span>';
        }
    })
    .catch(err => console.error('Like error:', err));
}

/**
 * Setup comment handlers
 */
function setupCommentHandlers() {
    document.querySelectorAll('form[action*="/post/"][action*="/comment"]').forEach(form => {
        form.addEventListener('submit', handleComment);
    });
}

/**
 * Handle comment submission
 */
function handleComment(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const postId = this.action.match(/\/post\/(\d+)/)[1];
    
    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Clear textarea
            this.querySelector('textarea').value = '';
            // Reload comments
            location.reload();
        }
    })
    .catch(err => console.error('Comment error:', err));
}

/**
 * Setup message handlers
 */
function setupMessageHandlers() {
    const msgForms = document.querySelectorAll('form[action*="/messages/send"]');
    msgForms.forEach(form => {
        form.addEventListener('submit', handleMessage);
    });
}

/**
 * Handle message submission
 */
function handleMessage(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Clear textarea
            this.querySelector('textarea').value = '';
            // Reload page or add message to DOM
            location.reload();
        }
    })
    .catch(err => console.error('Message error:', err));
}

/**
 * Initialize real-time notifications
 */
function initializeNotifications() {
    // Check for new notifications immediately and every 10 seconds
    checkNotifications();
    setInterval(checkNotifications, 10000);
}

/**
 * Check for new notifications
 */
function checkNotifications() {
    if (!document.querySelector('[data-user-id]')) return;
    
    fetch(APP_BASE_PATH + '/api/notifications?unread_only=1')
        .then(r => r.json())
        .then(data => {
            if (data.success && data.unread_count > 0) {
                updateNotificationBadge(data.unread_count);
                // Could show browser notification here
            }
        })
        .catch(err => console.error('Notification error:', err));
}

/**
 * Update notification badge
 */
function updateNotificationBadge(count) {
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline' : 'none';
    }
}

/**
 * API Helper function
 */
async function apiCall(url, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': CSRF_TOKEN
        }
    };
    
    if (data) {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(url, options);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        return { success: false, error: error.message };
    }
}

/**
 * Utility function to show toast/alert
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.textContent = message;
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '1000';
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

/**
 * Format date to readable format
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    
    if (date.toDateString() === today.toDateString()) {
        return 'Today at ' + date.toLocaleTimeString();
    } else if (date.toDateString() === yesterday.toDateString()) {
        return 'Yesterday at ' + date.toLocaleTimeString();
    } else {
        return date.toLocaleDateString();
    }
}

// Export for use in other scripts
window.EduConnect = {
    apiCall,
    showToast,
    formatDate
};

