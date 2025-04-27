<?php
session_start();
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
$email = $_GET['email'] ?? '';
$pageTitle = "Xác nhận tài khoản - FStyle";
include("../../includes/header.php");
?>

<div class="flex flex-col min-h-screen justify-center items-center bg-gray-50 px-4">
    <div class="w-full max-w-md bg-white p-8 rounded-3xl shadow-2xl mt-6 mb-12">
        <h2 class="text-3xl font-extrabold text-center text-primary mb-6">🔒 Xác nhận tài khoản</h2>

        <?php if (!empty($errors['general'])): ?>
        <div class="bg-red-100 text-red-700 border border-red-400 px-4 py-3 rounded-lg mb-5 text-center animate-pulse">
            <?= htmlspecialchars($errors['general']) ?>
        </div>
        <?php endif; ?>

        <p class="text-gray-600 text-center mb-6">
            Một mã xác nhận đã được gửi tới<br>
            <span class="font-semibold text-primary"><?= htmlspecialchars($email) ?></span>
        </p>

        <form action="../../controller/authController.php?action=verify" method="post" class="space-y-6"
            onsubmit="return collectCode()">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <input type="hidden" name="verify_code" id="verify_code_hidden">

            <label for="codeInputs" class="block text-sm font-semibold text-gray-700 mb-2">Mã xác nhận</label>

            <div id="codeInputs" class="flex justify-center gap-3">
                <?php for ($i = 0; $i < 6; $i++): ?>
                <input type="text" maxlength="1" class="w-12 h-14 text-center border rounded-xl text-2xl tracking-widest
                                  focus:outline-none focus:ring-2 focus:ring-primary border-gray-300
                                  bg-gray-100 hover:bg-white transition duration-200" oninput="moveNext(this, event)"
                    onpaste="handlePaste(event)" pattern="[0-9]*" inputmode="numeric" required>
                <?php endfor; ?>
            </div>

            <button type="submit"
                class="w-full bg-green-500 hover:bg-green-600 active:bg-green-700 text-white py-3 rounded-xl font-bold text-lg transition-all duration-300">
                Xác nhận
            </button>
        </form>
    </div>
</div>

<script>
function moveNext(input, event) {
    const inputs = Array.from(document.querySelectorAll('#codeInputs input'));
    const index = inputs.indexOf(input);

    if (event.inputType === 'deleteContentBackward') {
        if (input.value === '' && index > 0) {
            inputs[index - 1].focus();
            inputs[index - 1].select();
        }
    } else if (input.value.length === 1 && index < inputs.length - 1) {
        inputs[index + 1].focus();
        inputs[index + 1].select();
    }
}

function handlePaste(event) {
    event.preventDefault();
    const paste = (event.clipboardData || window.clipboardData).getData('text').trim().replace(/\D/g, '').slice(0, 6);
    const inputs = document.querySelectorAll('#codeInputs input');
    for (let i = 0; i < paste.length && i < inputs.length; i++) {
        inputs[i].value = paste[i];
    }
    if (paste.length === inputs.length) {
        inputs[inputs.length - 1].focus();
    }
}

function collectCode() {
    const inputs = document.querySelectorAll('#codeInputs input');
    let code = '';
    inputs.forEach(input => code += input.value);
    document.getElementById('verify_code_hidden').value = code;
    return true;
}
</script>

<?php include("../../includes/footer.php"); ?>