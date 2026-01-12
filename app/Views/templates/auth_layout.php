<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - SIMACCA</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#6B7280',
                        success: '#10B981',
                        warning: '#F59E0B',
                        danger: '#EF4444',
                        info: '#3ABFF8'
                    }
                }
            }
        }
    </script>
    
    <!-- Custom Styles -->
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .auth-card {
            animation: fadeInUp 0.5s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Custom focus styles */
        input:focus {
            outline: none;
            ring: 2px;
            ring-color: #667eea;
        }
    </style>
    
    <?= $this->renderSection('styles') ?>
</head>

<body class="flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-2xl auth-card">
        
        <!-- Header Section -->
        <div class="text-center">
            <?= $this->renderSection('header') ?>
        </div>
        
        <!-- Flash Messages -->
        <?= $this->include('components/alerts') ?>
        
        <!-- Main Content -->
        <div class="mt-8">
            <?= $this->renderSection('content') ?>
        </div>
        
        <!-- Footer Section (optional) -->
        <?= $this->renderSection('footer') ?>
        
    </div>
    
    <!-- Scripts -->
    <script>
        // Auto-hide flash messages after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
    
    <?= $this->renderSection('scripts') ?>
    
</body>

</html>
