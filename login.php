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
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;1,400;1,700&family=Manrope:wght@800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3 { font-family: 'Manrope', sans-serif; }
        .bg-primary { background-color: #0053dc; }
        .text-primary { color: #0053dc; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
        .italic-force { font-style: italic !important; }
    </style>
</head>
<body class="bg-[#f8fafc] flex items-center justify-center min-h-screen p-6 relative overflow-hidden italic-force">
    <!-- Decorative Orbs -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-100 rounded-full blur-[120px] opacity-60"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-100 rounded-full blur-[120px] opacity-60"></div>

    <div class="max-w-md w-full relative z-10">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-3xl shadow-2xl shadow-blue-100 mb-6 group hover:scale-110 transition-transform duration-500">
                <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight leading-none italic-force">KMS Bank Soal</h1>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.3em] mt-4 italic-force">SMA Kristen Kalam Kudus Malang</p>
        </div>

        <div class="bg-white rounded-[40px] shadow-2xl shadow-blue-100 border border-gray-100 p-10 relative overflow-hidden">
            <!-- Mode Switcher -->
            <div class="flex p-1.5 bg-gray-50 rounded-[24px] mb-10 shadow-inner">
                <button onclick="switchMode('<?php echo LOGIN_MODE_STANDARD; ?>')" id="tab-standard" 
                    class="flex-1 py-3.5 rounded-[20px] text-[11px] font-bold uppercase tracking-widest transition-all duration-300 <?php echo $login_mode == LOGIN_MODE_STANDARD ? 'bg-white shadow-lg text-primary' : 'text-gray-400 hover:text-gray-600'; ?>">
                    Pimpinan & Staf
                </button>
                <button onclick="switchMode('<?php echo LOGIN_MODE_PIN; ?>')" id="tab-pin" 
                    class="flex-1 py-3.5 rounded-[20px] text-[11px] font-bold uppercase tracking-widest transition-all duration-300 <?php echo $login_mode == LOGIN_MODE_PIN ? 'bg-white shadow-lg text-primary' : 'text-gray-400 hover:text-gray-600'; ?>">
                    Login Guru (PIN)
                </button>
            </div>

            <?php if ($error): ?>
                <div class="mb-8 p-5 bg-red-50 border border-red-100 rounded-3xl text-red-600 text-xs font-bold italic-force flex items-center">
                    <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" id="loginForm" class="space-y-6">
                <input type="hidden" name="login_mode" id="login_mode" value="<?php echo $login_mode; ?>">

                <!-- Standard UI (STAFF Table) -->
                <div id="standard-fields" class="<?php echo $login_mode == LOGIN_MODE_STANDARD ? '' : 'hidden'; ?> space-y-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Username</label>
                        <input type="text" name="username" placeholder="Masukkan username" 
                            class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner italic-force">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Password</label>
                        <input type="password" name="password" placeholder="••••••••" 
                            class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner italic-force">
                    </div>
                </div>

                <!-- PIN UI (TEACHERS Table) -->
                <div id="pin-fields" class="<?php echo $login_mode == LOGIN_MODE_PIN ? '' : 'hidden'; ?> space-y-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1 text-primary">Pilih Nama Guru</label>
                        <select name="teacher_id" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner italic-force">
                            <option value="">-- Pilih Guru --</option>
                            <?php foreach ($all_teachers as $t): ?>
                                <option value="<?php echo $t['id']; ?>">[<?php echo $t['nip']; ?>] - <?php echo $t['full_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Masukkan PIN (6 Digit)</label>
                        <input type="password" name="pin" maxlength="6" placeholder="123456" 
                            class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner text-center text-3xl tracking-[1em] font-bold">
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full bg-primary text-white py-5 rounded-3xl font-bold hover:bg-black transition-all duration-300 shadow-xl shadow-blue-100 flex items-center justify-center group overflow-hidden relative">
                        <span class="relative z-10 group-hover:scale-110 transition-transform">Masuk ke Portal KMS</span>
                        <div class="absolute inset-0 bg-gray-900 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                    </button>
                </div>
            </form>
        </div>

        <p class="mt-10 text-center text-[10px] text-gray-400 font-bold uppercase tracking-widest italic-force">
            &copy; 2026 Academic Knowledge Management System<br>
            SMA Kristen Kalam Kudus Malang
        </p>
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
