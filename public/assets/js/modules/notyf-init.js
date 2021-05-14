// Custom Notyf Integration
// Add any custom types or modify global duration from this file.

const notyf = new Notyf({
    duration: 3000,
    dismissible: true,
    position: {
        x: 'center',
        y: 'top',
    },
    types: [
        {
            type: 'info',
            backgroundColor: "#60A5FA",
            icon: {
                className: 'fa fa-info',
                color: '#fff'
            },
        },
        {
            type: 'warning',
            backgroundColor: "#ffc107",
            icon: {
                className: 'fa fa-exclamation-triangle',
                color: '#fff'
            },
        }
    ]
});

// Global Notyf Alerts
window.livewire.on('globalInfo', msg => {
    notyf.open({
        type: 'info',
        message: msg
    });
});
window.livewire.on('globalSuccess', msg => {
    notyf.open({
        type: 'success',
        message: msg
    });
});
window.livewire.on('globalError', msg => {
    notyf.open({
        type: 'error',
        message: msg
    });
});
window.livewire.on('globalWarning', msg => {
    notyf.open({
        type: 'warning',
        message: msg
    });
});