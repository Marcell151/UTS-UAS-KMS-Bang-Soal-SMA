<?php
// login.php
require_once 'config/database.php';
require_once 'config/constants.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';
$login_mode = $_POST['login_mode'] ?? LOGIN_MODE_STANDARD;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($login_mode === LOGIN_MODE_STANDARD) {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!empty($username) && !empty($password)) {
            $stmt = $pdo->prepare("SELECT s.*, r.role_name 
                                   FROM staff s 
                                   JOIN roles r ON s.role_id = r.id 
                                   WHERE s.username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && verifyAuth($password, $user['password'])) {
                $_SESSION['identity_id'] = $user['identity_id'];
                $_SESSION['actor_type'] = ACTOR_STAFF;
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['role_name'] = $user['role_name'];

                $stmt = $pdo->prepare("INSERT INTO logs (actor_id, action, ip_address) VALUES (?, ?, ?)");
                $stmt->execute([$user['identity_id'], 'Staff login (Standard)', $_SERVER['REMOTE_ADDR']]);

                header('Location: index.php');
                exit();
            } else {
                $error = 'Username atau password salah.';
            }
        }
    } else {
        $teacher_id = $_POST['teacher_id'] ?? '';
        $pin = $_POST['pin'] ?? '';

        if (!empty($teacher_id) && !empty($pin)) {
            $stmt = $pdo->prepare("SELECT * FROM teachers WHERE id = ?");
            $stmt->execute([$teacher_id]);
            $teacher = $stmt->fetch();

            if ($teacher && verifyAuth($pin, $teacher['pin'])) {
                $_SESSION['identity_id'] = $teacher['identity_id'];
                $_SESSION['actor_type'] = ACTOR_TEACHER;
                $_SESSION['full_name'] = $teacher['full_name'];
                $_SESSION['role_id'] = ROLE_GURU; // Logical Role for Guru
                $_SESSION['role_name'] = 'Guru';

                $stmt = $pdo->prepare("INSERT INTO logs (actor_id, action, ip_address) VALUES (?, ?, ?)");
                $stmt->execute([$teacher['identity_id'], 'Teacher login (PIN)', $_SERVER['REMOTE_ADDR']]);

                header('Location: index.php');
                exit();
            } else {
                $error = 'PIN yang Anda masukkan salah.';
            }
        }
    }
}

// Fetch Teachers for dropdown (Master Guru Table)
$stmt = $pdo->prepare("SELECT id, nip, full_name FROM teachers ORDER BY full_name ASC");
$stmt->execute();
$all_teachers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KMS Bank Soal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#003366', // Kalam Kudus Navy
                        accent: '#E30613',  // Kalam Kudus Red
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; color: #1E293B; }
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.5); }
    </style>
</head>
<body class="bg-[#F8FAFC] flex items-center justify-center min-h-screen p-6 relative overflow-hidden">
    <!-- Decorative Accents -->
    <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-blue-100 rounded-full blur-[120px] opacity-40"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-red-50 rounded-full blur-[120px] opacity-40"></div>

    <div class="max-w-md w-full relative z-10">
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-28 h-28 bg-white rounded-[40px] shadow-2xl shadow-blue-100 mb-8 p-6 border border-gray-50">
                <img src="upload/logo/Logo.png" alt="Logo Kalam Kudus" class="w-full h-auto object-contain">
            </div>
            <h1 class="text-4xl font-black text-[#003366] tracking-tighter leading-none">Portal KMS</h1>
            <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.4em] mt-4">SMA Kristen Kalam Kudus Malang</p>
        </div>

        <div class="bg-white rounded-[48px] shadow-2xl shadow-blue-100 border border-gray-100 p-12">
            <!-- Mode Switcher -->
            <div class="flex p-2 bg-gray-50 rounded-[32px] mb-12">
                <button onclick="switchMode('<?php echo LOGIN_MODE_STANDARD; ?>')" id="tab-standard" 
                    class="flex-1 py-4 rounded-[24px] text-[10px] font-black uppercase tracking-widest transition-all duration-300 <?php echo $login_mode == LOGIN_MODE_STANDARD ? 'bg-white shadow-lg text-[#003366]' : 'text-gray-400 hover:text-gray-600'; ?>">
                    Staf & Pimpinan
                </button>
                <button onclick="switchMode('<?php echo LOGIN_MODE_PIN; ?>')" id="tab-pin" 
                    class="flex-1 py-4 rounded-[24px] text-[10px] font-black uppercase tracking-widest transition-all duration-300 <?php echo $login_mode == LOGIN_MODE_PIN ? 'bg-white shadow-lg text-[#003366]' : 'text-gray-400 hover:text-gray-600'; ?>">
                    Portal Guru
                </button>
            </div>

            <?php if ($error): ?>
                <div class="mb-10 p-6 bg-red-50 border border-red-100 rounded-3xl text-red-600 text-xs font-bold flex items-center">
                    <svg class="w-5 h-5 mr-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" id="loginForm" class="space-y-8">
                <input type="hidden" name="login_mode" id="login_mode" value="<?php echo $login_mode; ?>">

                <!-- Standard UI (STAFF Table) -->
                <div id="standard-fields" class="<?php echo $login_mode == LOGIN_MODE_STANDARD ? '' : 'hidden'; ?> space-y-8">
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Username / ID</label>
                        <input type="text" name="username" placeholder="Masukkan ID pengguna" 
                            class="w-full px-8 py-5 bg-gray-50 border-none rounded-3xl outline-none focus:ring-2 focus:ring-[#003366] transition shadow-inner font-semibold">
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kata Sandi</label>
                        <input type="password" name="password" placeholder="••••••••" 
                            class="w-full px-8 py-5 bg-gray-50 border-none rounded-3xl outline-none focus:ring-2 focus:ring-[#003366] transition shadow-inner font-semibold">
                    </div>
                </div>

                <!-- PIN UI (TEACHERS Table) -->
                <div id="pin-fields" class="<?php echo $login_mode == LOGIN_MODE_PIN ? '' : 'hidden'; ?> space-y-8">
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Identitas Guru</label>
                        <select name="teacher_id" class="w-full px-8 py-5 bg-gray-50 border-none rounded-3xl outline-none focus:ring-2 focus:ring-[#003366] transition shadow-inner font-bold">
                            <option value="">-- Pilih Nama Anda --</option>
                            <?php foreach ($all_teachers as $t): ?>
                                <option value="<?php echo $t['id']; ?>"><?php echo $t['full_name']; ?> [<?php echo $t['nip']; ?>]</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-center">PIN Keamanan (6 Digit)</label>
                        <div class="flex justify-center">
                            <input type="password" name="pin" maxlength="6" placeholder="••••••" 
                                class="w-full max-w-[280px] h-20 bg-gray-50 border-none rounded-[32px] outline-none focus:ring-2 focus:ring-[#003366] transition shadow-inner text-center text-3xl tracking-[0.5em] font-black placeholder:text-gray-300 placeholder:tracking-normal">
                        </div>
                        <p class="text-[9px] text-gray-400 text-center font-bold italic">Masukkan 6 digit kode akses Anda</p>
                    </div>
                </div>

                <div class="pt-8">
                    <button type="submit" class="w-full bg-[#003366] text-white py-6 rounded-3xl font-black hover:bg-black transition-all duration-300 shadow-2xl shadow-blue-100 flex items-center justify-center group relative overflow-hidden">
                        <span class="relative z-10 group-hover:tracking-widest transition-all duration-300 uppercase text-xs">Akses Sistem KMS</span>
                        <div class="absolute inset-0 bg-red-600 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-12 text-center">
            <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.3em] leading-relaxed">
                Knowledge Management System<br>
                <span class="text-red-600">SMA KRISTEN KALAM KUDUS MALANG</span>
            </p>
        </div>
    </div>

    <script>
        function switchMode(mode) {
            document.getElementById('login_mode').value = mode;
            const standardFields = document.getElementById('standard-fields');
            const pinFields = document.getElementById('pin-fields');
            const tabStandard = document.getElementById('tab-standard');
            const tabPin = document.getElementById('tab-pin');

            if (mode === 'standard') {
                standardFields.classList.remove('hidden');
                pinFields.classList.add('hidden');
                tabStandard.classList.add('bg-white', 'shadow-lg', 'text-primary');
                tabStandard.classList.remove('text-gray-400');
                tabPin.classList.remove('bg-white', 'shadow-lg', 'text-primary');
                tabPin.classList.add('text-gray-400');
            } else {
                standardFields.classList.add('hidden');
                pinFields.classList.remove('hidden');
                tabPin.classList.add('bg-white', 'shadow-lg', 'text-primary');
                tabPin.classList.remove('text-gray-400');
                tabStandard.classList.remove('bg-white', 'shadow-lg', 'text-primary');
                tabStandard.classList.add('text-gray-400');
            }
        }
    </script>
</body>
</html>
