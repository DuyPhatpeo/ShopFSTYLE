<?php
session_start();
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
$email = $_GET['email'] ?? '';
$pageTitle = "Xác nhận tài khoản - FStyle";
include("../../includes/header.php");
?>

<div class="flex flex-col min-h-screen justify-center">
    <div class="max-w-md mx-auto bg-white p-6 rounded-3xl shadow-lg mt-12 mb-12 w-full sm:w-96">
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-primary mb-6">Xác nhận tài khoản</h2>

        <?php if (!empty($errors['general'])): ?>
        <div class="bg-red-100 text-red-700 border border-red-400 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($errors['general']) ?>
        </div>
        <?php endif; ?>

        <p class="text-center mb-4">
            Chúng tôi đã gửi mã xác nhận đến email:
            <strong><?= htmlspecialchars($email) ?></strong>
        </p>

        <form action="../../controller/authController.php?action=verify" method="post" class="space-y-5"
            onsubmit="return collectCode()">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <input type="hidden" name="verify_code" id="verify_code_hidden">

            <label for="codeInputs" class="block text-sm font-medium text-gray-700 mb-1">Nhập mã xác nhận</label>
            <div id="codeInputs" class="flex justify-between gap-2">
                <?php for ($i = 0; $i < 6; $i++): ?>
                <input type="text" maxlength="1"
                    class="w-12 h-12 text-center border rounded-lg text-xl tracking-widest focus:outline-none focus:ring-2 focus:ring-primary border-gray-300"
                    oninput="moveNext(this, event)" onpaste="handlePaste(event)" pattern="[0-9]*" inputmode="numeric"
                    required>
                <?php endfor; ?>
            </div>

            <button type="submit"
                class="w-full bg-green-500 text-white py-3 rounded-lg hover:bg-green-600 transition font-semibold text-lg mt-4">
                Xác nhận
            </button>
        </form>
    </div>
</div>

<script>
function moveNext(input, event) {
    const inputs = Array.from(document.querySelectorAll('#codeInputs input'));
    const index = inputs.indexOf(input);

    // Chuyển focus khi gõ hoặc xóa
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

// Tự động điền nếu paste nguyên mã
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

// Gộp lại mã xác nhận trước khi submit
function collectCode() {
    const inputs = document.querySelectorAll('#codeInputs input');
    let code = '';
    inputs.forEach(input => code += input.value);
    document.getElementById('verify_code_hidden').value = code;
    return true; // Cho phép submit
}
</script>

<?php include("../../includes/footer.php"); ?>