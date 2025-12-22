import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    wsHost: window.location.hostname,
    wsPort: 6001,
    disableStats: true,
});

// Listen for expense events
window.Echo.channel('expenses')
    .listen('.expense.created', (data) => {
        // Handle expense created
        console.log('Expense created:', data);
        if (window.Livewire) {
            window.Livewire.dispatch('expense-created', { expense: data.expense });
        } else {
            // Fallback for non-Livewire pages
            window.location.reload();
        }
    })
    .listen('.expense.updated', (data) => {
        // Handle expense updated
        console.log('Expense updated:', data);
        if (window.Livewire) {
            window.Livewire.dispatch('expense-updated', { expense: data.expense });
        } else {
            // Fallback for non-Livewire pages
            window.location.reload();
        }
    })
    .listen('.expense.deleted', (data) => {
        // Handle expense deleted
        console.log('Expense deleted:', data);
        if (window.Livewire) {
            window.Livewire.dispatch('expense-deleted', { expenseId: data.expense.id });
        } else {
            // Fallback for non-Livewire pages
            window.location.reload();
        }
    });
