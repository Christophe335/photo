<?php
$seo_title = 'Préférences cookies - Album Photo Book';
$seo_description = 'Gérez vos préférences de cookies et confidentialité.';
include __DIR__ . '/../includes/header.php';
?>
<main class="cadre">
    <div class="container">
        <h1>Préférences cookies</h1>
        <p>Vous pouvez activer ou désactiver les cookies suivants :</p>
        <form method="post" action="<?php echo isset($basePath) ? $basePath : ''; ?>formulaires/cookie-preferences.php">
            <label><input type="checkbox" name="analytics" checked> Analytics (anonymisé)</label><br>
            <label><input type="checkbox" name="marketing"> Marketing</label><br>
            <label><input type="checkbox" name="functional" checked> Fonctionnels</label><br>
            <button type="submit">Enregistrer</button>
        </form>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
