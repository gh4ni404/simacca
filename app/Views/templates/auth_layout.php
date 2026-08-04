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
    <?php $faviconLogo = get_logo_sekolah(); ?>
    <?php if ($faviconLogo): ?>
        <link rel="shortcut icon" type="image/png" href="<?= base_url('files/logo/' . $faviconLogo) ?>">
    <?php else: ?>
        <link rel="shortcut icon" type="image/png" href="<?= base_url('favicon.ico') ?>">
    <?php endif; ?>
    
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
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #6d28d9 100%);
            min-height: 100vh;
            overflow: hidden;
        }

        /* Logo background pattern */
        .logo-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }
        .logo-bg img {
            position: absolute;
            object-fit: contain;
        }
        /* ---- Layer 1: besar, blur banyak, opacity rendah (kedalaman jauh) ---- */
        .logo-bg .a1  { width: 180px; top: -5%;  left: -3%;  transform: rotate(-20deg); opacity: 0.06; filter: blur(6px); }
        .logo-bg .a2  { width: 220px; top: 10%;  left: 60%;  transform: rotate(15deg);  opacity: 0.05; filter: blur(8px); }
        .logo-bg .a3  { width: 200px; top: 70%;  left: 75%;  transform: rotate(-35deg); opacity: 0.06; filter: blur(7px); }
        .logo-bg .a4  { width: 190px; top: 80%;  left: -5%;  transform: rotate(25deg);  opacity: 0.05; filter: blur(9px); }
        .logo-bg .a5  { width: 170px; top: 40%;  left: 85%;  transform: rotate(-10deg); opacity: 0.06; filter: blur(6px); }
        .logo-bg .a6  { width: 210px; top: -8%;  left: 35%;  transform: rotate(30deg);  opacity: 0.05; filter: blur(8px); }
        .logo-bg .a7  { width: 195px; top: 55%;  left: -8%;  transform: rotate(-28deg); opacity: 0.06; filter: blur(7px); }
        .logo-bg .a8  { width: 185px; top: 25%;  left: 70%;  transform: rotate(20deg);  opacity: 0.05; filter: blur(9px); }
        .logo-bg .a9  { width: 205px; top: 90%;  left: 50%;  transform: rotate(-15deg); opacity: 0.06; filter: blur(6px); }
        .logo-bg .a10 { width: 175px; top: 15%;  left: 20%;  transform: rotate(35deg);  opacity: 0.05; filter: blur(8px); }
        .logo-bg .a11 { width: 215px; top: 65%;  left: 30%;  transform: rotate(-5deg);  opacity: 0.06; filter: blur(7px); }
        .logo-bg .a12 { width: 188px; top: 45%;  left: 55%;  transform: rotate(22deg);  opacity: 0.05; filter: blur(8px); }

        /* ---- Layer 2: sedang, blur sedang, opacity lebih tinggi ---- */
        .logo-bg .b1  { width: 120px; top: 5%;   left: 15%;  transform: rotate(-15deg); opacity: 0.10; filter: blur(3px); }
        .logo-bg .b2  { width: 100px; top: 8%;   left: 72%;  transform: rotate(22deg);  opacity: 0.09; filter: blur(4px); }
        .logo-bg .b3  { width: 140px; top: 25%;  left: 5%;   transform: rotate(-28deg); opacity: 0.10; filter: blur(3px); }
        .logo-bg .b4  { width: 110px; top: 20%;  left: 45%;  transform: rotate(18deg);  opacity: 0.08; filter: blur(4px); }
        .logo-bg .b5  { width: 130px; top: 15%;  left: 88%;  transform: rotate(-8deg);  opacity: 0.10; filter: blur(3px); }
        .logo-bg .b6  { width: 95px;  top: 38%;  left: 78%;  transform: rotate(35deg);  opacity: 0.09; filter: blur(4px); }
        .logo-bg .b7  { width: 115px; top: 50%;  left: 10%;  transform: rotate(-22deg); opacity: 0.10; filter: blur(3px); }
        .logo-bg .b8  { width: 105px; top: 65%;  left: 55%;  transform: rotate(12deg);  opacity: 0.08; filter: blur(4px); }
        .logo-bg .b9  { width: 125px; top: 75%;  left: 30%;  transform: rotate(-18deg); opacity: 0.10; filter: blur(3px); }
        .logo-bg .b10 { width: 90px;  top: 88%;  left: 65%;  transform: rotate(28deg);  opacity: 0.09; filter: blur(4px); }
        .logo-bg .b11 { width: 135px; top: 55%;  left: 40%;  transform: rotate(-5deg);  opacity: 0.10; filter: blur(3px); }
        .logo-bg .b12 { width: 100px; top: 35%;  left: 25%;  transform: rotate(40deg);  opacity: 0.08; filter: blur(4px); }
        .logo-bg .b13 { width: 112px; top: 3%;   left: 50%;  transform: rotate(-32deg); opacity: 0.10; filter: blur(3px); }
        .logo-bg .b14 { width: 98px;  top: 42%;  left: 92%;  transform: rotate(10deg);  opacity: 0.09; filter: blur(4px); }
        .logo-bg .b15 { width: 128px; top: 60%;  left: 18%;  transform: rotate(-25deg); opacity: 0.10; filter: blur(3px); }
        .logo-bg .b16 { width: 108px; top: 82%;  left: 42%;  transform: rotate(30deg);  opacity: 0.08; filter: blur(4px); }
        .logo-bg .b17 { width: 118px; top: 12%;  left: 35%;  transform: rotate(-12deg); opacity: 0.10; filter: blur(3px); }
        .logo-bg .b18 { width: 92px;  top: 70%;  left: 80%;  transform: rotate(38deg);  opacity: 0.09; filter: blur(4px); }
        .logo-bg .b19 { width: 138px; top: 28%;  left: 62%;  transform: rotate(-3deg);  opacity: 0.10; filter: blur(3px); }
        .logo-bg .b20 { width: 102px; top: 92%;  left: 12%;  transform: rotate(20deg);  opacity: 0.08; filter: blur(4px); }
        .logo-bg .b21 { width: 122px; top: 48%;  left: 72%;  transform: rotate(-18deg); opacity: 0.10; filter: blur(3px); }
        .logo-bg .b22 { width: 88px;  top: 18%;  left: 82%;  transform: rotate(42deg);  opacity: 0.09; filter: blur(4px); }
        .logo-bg .b23 { width: 132px; top: 78%;  left: 58%;  transform: rotate(-8deg);  opacity: 0.10; filter: blur(3px); }
        .logo-bg .b24 { width: 96px;  top: 58%;  left: 28%;  transform: rotate(15deg);  opacity: 0.08; filter: blur(4px); }

        /* ---- Layer 3: kecil, blur minim, opacity tinggi (kedalaman dekat) ---- */
        .logo-bg .c1  { width: 65px;  top: 3%;   left: 42%;  transform: rotate(-12deg); opacity: 0.14; filter: blur(1px); }
        .logo-bg .c2  { width: 55px;  top: 18%;  left: 20%;  transform: rotate(32deg);  opacity: 0.13; filter: blur(1px); }
        .logo-bg .c3  { width: 70px;  top: 12%;  left: 82%;  transform: rotate(-25deg); opacity: 0.15; filter: blur(0px); }
        .logo-bg .c4  { width: 50px;  top: 30%;  left: 60%;  transform: rotate(15deg);  opacity: 0.14; filter: blur(1px); }
        .logo-bg .c5  { width: 60px;  top: 42%;  left: 18%;  transform: rotate(-38deg); opacity: 0.13; filter: blur(0px); }
        .logo-bg .c6  { width: 75px;  top: 36%;  left: 92%;  transform: rotate(8deg);   opacity: 0.15; filter: blur(1px); }
        .logo-bg .c7  { width: 55px;  top: 58%;  left: 68%;  transform: rotate(-20deg); opacity: 0.14; filter: blur(0px); }
        .logo-bg .c8  { width: 65px;  top: 62%;  left: 5%;   transform: rotate(28deg);  opacity: 0.13; filter: blur(1px); }
        .logo-bg .c9  { width: 50px;  top: 72%;  left: 48%;  transform: rotate(-32deg); opacity: 0.15; filter: blur(0px); }
        .logo-bg .c10 { width: 60px;  top: 82%;  left: 82%;  transform: rotate(18deg);  opacity: 0.14; filter: blur(1px); }
        .logo-bg .c11 { width: 70px;  top: 90%;  left: 15%;  transform: rotate(-8deg);  opacity: 0.13; filter: blur(0px); }
        .logo-bg .c12 { width: 45px;  top: 48%;  left: 52%;  transform: rotate(42deg);  opacity: 0.15; filter: blur(1px); }
        .logo-bg .c13 { width: 55px;  top: 28%;  left: 38%;  transform: rotate(-45deg); opacity: 0.14; filter: blur(0px); }
        .logo-bg .c14 { width: 60px;  top: 78%;  left: 58%;  transform: rotate(10deg);  opacity: 0.13; filter: blur(1px); }
        .logo-bg .c15 { width: 50px;  top: 55%;  left: 90%;  transform: rotate(-15deg); opacity: 0.15; filter: blur(0px); }
        .logo-bg .c16 { width: 65px;  top: 95%;  left: 42%;  transform: rotate(35deg);  opacity: 0.14; filter: blur(1px); }
        .logo-bg .c17 { width: 52px;  top: 7%;   left: 65%;  transform: rotate(-22deg); opacity: 0.13; filter: blur(0px); }
        .logo-bg .c18 { width: 58px;  top: 22%;  left: 48%;  transform: rotate(25deg);  opacity: 0.15; filter: blur(1px); }
        .logo-bg .c19 { width: 48px;  top: 35%;  left: 8%;   transform: rotate(-40deg); opacity: 0.14; filter: blur(0px); }
        .logo-bg .c20 { width: 62px;  top: 50%;  left: 75%;  transform: rotate(5deg);   opacity: 0.13; filter: blur(1px); }
        .logo-bg .c21 { width: 54px;  top: 65%;  left: 35%;  transform: rotate(-30deg); opacity: 0.15; filter: blur(0px); }
        .logo-bg .c22 { width: 46px;  top: 85%;  left: 72%;  transform: rotate(20deg);  opacity: 0.14; filter: blur(1px); }
        .logo-bg .c23 { width: 68px;  top: 15%;  left: 55%;  transform: rotate(-5deg);  opacity: 0.13; filter: blur(0px); }
        .logo-bg .c24 { width: 56px;  top: 45%;  left: 32%;  transform: rotate(38deg);  opacity: 0.15; filter: blur(1px); }
        .logo-bg .c25 { width: 44px;  top: 75%;  left: 18%;  transform: rotate(-18deg); opacity: 0.14; filter: blur(0px); }
        .logo-bg .c26 { width: 64px;  top: 88%;  left: 88%;  transform: rotate(12deg);  opacity: 0.13; filter: blur(1px); }
        .logo-bg .c27 { width: 50px;  top: 5%;   left: 90%;  transform: rotate(-35deg); opacity: 0.15; filter: blur(0px); }
        .logo-bg .c28 { width: 58px;  top: 60%;  left: 48%;  transform: rotate(28deg);  opacity: 0.14; filter: blur(1px); }
        .logo-bg .c29 { width: 42px;  top: 40%;  left: 70%;  transform: rotate(-10deg); opacity: 0.13; filter: blur(0px); }
        .logo-bg .c30 { width: 66px;  top: 92%;  left: 25%;  transform: rotate(45deg);  opacity: 0.15; filter: blur(1px); }
        .logo-bg .c31 { width: 52px;  top: 25%;  left: 12%;  transform: rotate(-28deg); opacity: 0.14; filter: blur(0px); }
        .logo-bg .c32 { width: 60px;  top: 70%;  left: 62%;  transform: rotate(32deg);  opacity: 0.13; filter: blur(1px); }

        /* Gradient overlay — match body gradient */
        .logo-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(79,70,229,0.55) 0%, rgba(124,58,237,0.45) 50%, rgba(109,40,217,0.55) 100%);
        }

        .auth-card {
            animation: fadeInUp 0.5s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        input:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(124,58,237,0.4);
        }
    </style>
    
    <?= $this->renderSection('styles') ?>
</head>

<body class="flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <!-- Logo Background Pattern -->
    <?php $authLogo = get_logo_sekolah(); ?>
    <?php if ($authLogo): ?>
    <div class="logo-bg">
        <!-- Layer 1: besar, blur banyak -->
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="a1" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="a2" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="a3" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="a4" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="a5" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="a6" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="a7" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="a8" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="a9" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="a10" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="a11" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="a12" alt="">
        <!-- Layer 2: sedang -->
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b1" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b2" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b3" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b4" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b5" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b6" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b7" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b8" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b9" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b10" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b11" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b12" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b13" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b14" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b15" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b16" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b17" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b18" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b19" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b20" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b21" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b22" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b23" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="b24" alt="">
        <!-- Layer 3: kecil, tajam -->
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c1" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c2" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c3" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c4" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c5" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c6" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c7" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c8" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c9" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c10" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c11" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c12" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c13" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c14" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c15" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c16" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c17" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c18" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c19" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c20" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c21" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c22" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c23" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c24" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c25" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c26" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c27" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c28" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c29" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c30" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c31" alt="">
        <img src="<?= base_url('files/logo/' . $authLogo) ?>" class="c32" alt="">
    </div>
    <?php endif; ?>
    
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-2xl auth-card relative z-10">
        
        <!-- Header Section -->
        <div class="text-center">
            <?= $this->renderSection('header') ?>
        </div>
        
        <!-- Flash Messages -->
        <?= render_alerts() ?>
        
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