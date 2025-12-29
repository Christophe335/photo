<?php
session_start();
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/header.php';

// Override container width for cette page uniquement (rendre pleine largeur)
echo "<script>document.addEventListener('DOMContentLoaded',function(){var c=document.querySelector('.container');if(c){c.style.maxWidth='none';c.style.width='100%';c.style.padding='0 20px';}});</script>";

$db = Database::getInstance()->getConnection();

// Créer la table de liaison si elle n'existe pas (avec colonnes type et enabled)
try {
    $db->exec("CREATE TABLE IF NOT EXISTS personnalisation_liaisons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        produit_ref VARCHAR(100) NOT NULL,
        ref_pre_encollage VARCHAR(100) DEFAULT NULL,
        ref_impression VARCHAR(100) DEFAULT NULL,
        type VARCHAR(32) NOT NULL DEFAULT 'imprime',
        enabled TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (Exception $e) {
    // ignore
}

// Tentative d'ajout de colonnes (pour les installations antérieures) — vérification via INFORMATION_SCHEMA
try {
    $cols = [
        'type' => "VARCHAR(32) NOT NULL DEFAULT 'imprime'",
        'enabled' => "TINYINT(1) NOT NULL DEFAULT 1",
        'ref_pre_encollage' => "VARCHAR(100) DEFAULT NULL",
        'ref_impression' => "VARCHAR(100) DEFAULT NULL",
        'ref_impression_2' => "VARCHAR(100) DEFAULT NULL",
        'ref_impression_3' => "VARCHAR(100) DEFAULT NULL"
    ];

    $checkStmt = $db->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'personnalisation_liaisons' AND COLUMN_NAME = ?");
    foreach ($cols as $col => $definition) {
        try {
            $checkStmt->execute([$col]);
            $exists = intval($checkStmt->fetchColumn() ?? 0);
            if ($exists === 0) {
                $db->exec("ALTER TABLE personnalisation_liaisons ADD COLUMN $col $definition");
            }
        } catch (Exception $e) {
            // Ignore colonne non ajoutable, continuer
            error_log('Erreur ajout colonne ' . $col . ' : ' . $e->getMessage());
        }
    }
} catch (Exception $e) {
    // Si INFORMATION_SCHEMA indisponible, ignorer
    error_log('Vérif colonnes personnalisation_liaisons: ' . $e->getMessage());
}

// Traitement des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $produit_ref = trim($_POST['produit_ref'] ?? '');
    // Nouveaux champs
    $ref_pre_encollage = trim($_POST['ref_pre_encollage'] ?? '');
    $ref_impression = trim($_POST['ref_impression'] ?? '');
    $ref_impression_2 = trim($_POST['ref_impression_2'] ?? '');
    $ref_impression_3 = trim($_POST['ref_impression_3'] ?? '');
    $type = trim($_POST['type'] ?? 'imprime');
    $enabled = isset($_POST['enabled']) && $_POST['enabled'] ? 1 : 0;

    if ($action === 'add') {
        if (empty($produit_ref)) {
            $_SESSION['message'] = 'Référence produit vide — enregistrement annulé.';
            $_SESSION['message_type'] = 'error';
            header('Location: liaisons_personnalisation.php'); exit;
        }

        $ref_impression_final = $ref_impression ?: null;
        $ref_impression_2_final = $ref_impression_2 ?: null;
        $ref_impression_3_final = $ref_impression_3 ?: null;
        try {
            $sql = 'INSERT INTO personnalisation_liaisons (produit_ref, ref_pre_encollage, ref_impression, ref_impression_2, ref_impression_3, type, enabled) VALUES (?, ?, ?, ?, ?, ?, ?)';
            $params = [$produit_ref, $ref_pre_encollage ?: null, $ref_impression_final, $ref_impression_2_final, $ref_impression_3_final, $type, $enabled];
            // Debug: log la requête et les paramètres avant exécution
            error_log('DEBUG SQL INSERT personnalisation_liaisons: ' . $sql . ' -- PARAMS: ' . var_export($params, true));
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $_SESSION['message'] = 'Association ajoutée.';
            $_SESSION['message_type'] = 'success';
        } catch (Exception $e) {
            $err = $e->getMessage();
            $stmtInfo = isset($stmt) ? var_export($stmt->errorInfo(), true) : 'no_stmt';
            error_log('Erreur INSERT personnalisation_liaisons: ' . $err . ' -- STMT_INFO: ' . $stmtInfo . ' -- PARAMS: ' . var_export($params, true));
            $_SESSION['message'] = 'Erreur lors de l\'ajout : ' . $err;
            $_SESSION['message_type'] = 'error';
        }
        header('Location: liaisons_personnalisation.php'); exit;
    }

    if ($action === 'edit' && !empty($_POST['id'])) {
        $id = intval($_POST['id']);
        $ref_impression_final = $ref_impression ?: null;
        $ref_impression_2_final = $ref_impression_2 ?: null;
        $ref_impression_3_final = $ref_impression_3 ?: null;
        try {
            $sql = 'UPDATE personnalisation_liaisons SET produit_ref = ?, ref_pre_encollage = ?, ref_impression = ?, ref_impression_2 = ?, ref_impression_3 = ?, type = ?, enabled = ? WHERE id = ?';
            $params = [$produit_ref, $ref_pre_encollage ?: null, $ref_impression_final, $ref_impression_2_final, $ref_impression_3_final, $type, $enabled, $id];
            error_log('DEBUG SQL UPDATE personnalisation_liaisons: ' . $sql . ' -- PARAMS: ' . var_export($params, true));
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $_SESSION['message'] = 'Association mise à jour.';
            $_SESSION['message_type'] = 'success';
        } catch (Exception $e) {
            $err = $e->getMessage();
            $stmtInfo = isset($stmt) ? var_export($stmt->errorInfo(), true) : 'no_stmt';
            error_log('Erreur UPDATE personnalisation_liaisons id=' . $id . ' : ' . $err . ' -- STMT_INFO: ' . $stmtInfo . ' -- PARAMS: ' . var_export($params, true));
            $_SESSION['message'] = 'Erreur lors de la mise à jour : ' . $err;
            $_SESSION['message_type'] = 'error';
        }
        header('Location: liaisons_personnalisation.php'); exit;
    }
}

// Suppression via GET
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $db->prepare('DELETE FROM personnalisation_liaisons WHERE id = ?');
    $stmt->execute([$id]);
    $_SESSION['message'] = 'Association supprimée.';
    $_SESSION['message_type'] = 'success';
    header('Location: liaisons_personnalisation.php'); exit;
}

// Récupérer toutes les liaisons
$stmt = $db->query('SELECT * FROM personnalisation_liaisons ORDER BY id DESC');
$liaisons = $stmt->fetchAll();

// Helper pour récupérer la désignation d'une référence produit
function getDesignation($db, $ref) {
    $stmt = $db->prepare('SELECT designation FROM produits WHERE reference = ? LIMIT 1');
    $stmt->execute([$ref]);
    $r = $stmt->fetch();
    return $r ? $r['designation'] : '';
}

?>

<div class="admin-page" style="width:100%;max-width:none;padding:20px;">
    <h2>Gestion des liaisons de personnalisation</h2>

    <div style="display:flex;gap:20px;align-items:flex-start;">
        <div style="flex:0.5;">
            <h3>Ajouter / Modifier</h3>
            <form method="post" id="formLiaison">
                <input type="hidden" name="action" value="add" id="formAction">
                <input type="hidden" name="id" id="formId" value="">
                <div>
                    <label>Référence produit</label><br>
                    <input type="text" name="produit_ref" id="produit_ref" style="width:75%;padding:8px;margin-bottom:6px;">
                    <div id="produit_designation" style="color:#666;font-size:13px;margin-bottom:10px;"></div>
                </div>

                <div>
                    <label>Ref pré-encollage</label><br>
                    <input type="text" name="ref_pre_encollage" id="ref_pre_encollage" placeholder="Réf produit pré-encollage" style="width:75%;padding:8px;margin-bottom:6px;">
                    <div id="preencollage_designation" style="color:#666;font-size:13px;margin-bottom:10px;"></div>
                </div>

                <div>
                    <label>Ref Impression</label><br>
                    <input type="text" name="ref_impression" id="ref_impression" placeholder="Réf produit impression" style="width:75%;padding:8px;margin-bottom:6px;">
                    <div id="impression_designation" style="color:#666;font-size:13px;margin-bottom:10px;"></div>
                </div>

                <div>
                    <label>Ref Impression (format 2)</label><br>
                    <input type="text" name="ref_impression_2" id="ref_impression_2" placeholder="Réf produit impression format 2" style="width:75%;padding:8px;margin-bottom:6px;">
                    <div id="impression2_designation" style="color:#666;font-size:13px;margin-bottom:10px;"></div>
                </div>

                <div>
                    <label>Ref Impression (format 3)</label><br>
                    <input type="text" name="ref_impression_3" id="ref_impression_3" placeholder="Réf produit impression format 3" style="width:75%;padding:8px;margin-bottom:6px;">
                    <div id="impression3_designation" style="color:#666;font-size:13px;margin-bottom:10px;"></div>
                </div>

                <div style="margin-bottom:10px;">
                    <label style="display:inline-block;margin-right:8px;">Actif (dorure)</label>
                    <input type="checkbox" name="enabled" id="enabled" value="1" checked>
                    <small style="color:#666;margin-left:8px;">Cocher pour activer la dorure sur ce produit</small>
                </div>

                <div>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                    <button type="button" class="btn" id="btnReset">Réinitialiser</button>
                </div>
            </form>
        </div>

        <div style="flex:2;">
            <h3>Liste des associations</h3>
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#f5f5f5;text-align:left;">
                        <th style="padding:8px;border:1px solid #eee;">ID</th>
                        <th style="padding:8px;border:1px solid #eee;">Réf produit</th>
                        <th style="padding:8px;border:1px solid #eee;">Désignation produit</th>
                        <th style="padding:8px;border:1px solid #eee;">Ref pré-encollage</th>
                        <th style="padding:8px;border:1px solid #eee;">Ref Impression 1</th>
                        <th style="padding:8px;border:1px solid #eee;">Ref Impression 2</th>
                        <th style="padding:8px;border:1px solid #eee;">Ref Impression 3</th>
                        <th style="padding:8px;border:1px solid #eee;">Actif (dorure)</th>
                        <th style="padding:8px;border:1px solid #eee;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($liaisons as $l): ?>
                        <tr>
                            <td style="padding:8px;border:1px solid #eee;vertical-align:top;"><?= htmlspecialchars($l['id']) ?></td>
                            <td style="padding:8px;border:1px solid #eee;vertical-align:top;"><?= htmlspecialchars($l['produit_ref']) ?></td>
                            <td style="padding:8px;border:1px solid #eee;vertical-align:top;"><?= htmlspecialchars(getDesignation($db, $l['produit_ref'])) ?></td>
                            <td style="padding:8px;border:1px solid #eee;vertical-align:top;"><?= htmlspecialchars($l['ref_pre_encollage'] ?? '') ?></td>
                            <td style="padding:8px;border:1px solid #eee;vertical-align:top;"><?= htmlspecialchars($l['ref_impression'] ?? '') ?></td>
                            <td style="padding:8px;border:1px solid #eee;vertical-align:top;"><?= htmlspecialchars($l['ref_impression_2'] ?? '') ?></td>
                            <td style="padding:8px;border:1px solid #eee;vertical-align:top;"><?= htmlspecialchars($l['ref_impression_3'] ?? '') ?></td>
                            <td style="padding:8px;border:1px solid #eee;vertical-align:top;"><?= isset($l['enabled']) && $l['enabled'] ? 'Oui' : 'Non' ?></td>
                            <td style="display:flex;padding:8px;border:1px solid #eee;vertical-align:top;">
                                <a href="#" class="btn btn-small" onclick="editLiaison(<?= $l['id'] ?>,'<?= htmlspecialchars($l['produit_ref'], ENT_QUOTES) ?>','<?= htmlspecialchars($l['ref_pre_encollage'] ?? '', ENT_QUOTES) ?>','<?= htmlspecialchars($l['ref_impression'] ?? '', ENT_QUOTES) ?>','<?= htmlspecialchars($l['ref_impression_2'] ?? '', ENT_QUOTES) ?>','<?= htmlspecialchars($l['ref_impression_3'] ?? '', ENT_QUOTES) ?>',<?= intval($l['enabled'] ?? 0) ?>);return false;">Modifier</a>
                                <a href="?action=delete&id=<?= $l['id'] ?>" class="btn btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
document.getElementById('btnReset').addEventListener('click', function(){
    document.getElementById('formLiaison').reset();
    document.getElementById('formAction').value = 'add';
    document.getElementById('formId').value = '';
    document.getElementById('produit_designation').textContent = '';
    if (document.getElementById('preencollage_designation')) document.getElementById('preencollage_designation').textContent = '';
    if (document.getElementById('impression_designation')) document.getElementById('impression_designation').textContent = '';
    if (document.getElementById('impression2_designation')) document.getElementById('impression2_designation').textContent = '';
    if (document.getElementById('impression3_designation')) document.getElementById('impression3_designation').textContent = '';
});

function editLiaison(id, produitRef, refPre, refImpr, refImpr2, refImpr3, enabled){
    document.getElementById('formAction').value = 'edit';
    document.getElementById('formId').value = id;
    document.getElementById('produit_ref').value = produitRef;
    if (document.getElementById('ref_pre_encollage')) document.getElementById('ref_pre_encollage').value = refPre || '';
    if (document.getElementById('ref_impression')) document.getElementById('ref_impression').value = refImpr || '';
    if (document.getElementById('ref_impression_2')) document.getElementById('ref_impression_2').value = refImpr2 || '';
    if (document.getElementById('ref_impression_3')) document.getElementById('ref_impression_3').value = refImpr3 || '';
    if (document.getElementById('enabled')) document.getElementById('enabled').checked = enabled ? true : false;
    // Trigger lookup
    lookupRef(produitRef, 'produit_designation');
    lookupRef(refPre, 'preencollage_designation');
    lookupRef(refImpr, 'impression_designation');
    lookupRef(refImpr2, 'impression2_designation');
    lookupRef(refImpr3, 'impression3_designation');
    window.scrollTo({top:0,behavior:'smooth'});
}

function lookupRef(ref, targetId){
    if (!ref) return;
    fetch('../ajax/get-produit-par-ref.php?ref=' + encodeURIComponent(ref))
        .then(r => r.json())
        .then(data => {
            document.getElementById(targetId).textContent = data.designation || '';
        }).catch(e => console.error(e));
}

document.getElementById('produit_ref').addEventListener('blur', function(){ lookupRef(this.value, 'produit_designation'); });
if (document.getElementById('ref_pre_encollage')) document.getElementById('ref_pre_encollage').addEventListener('blur', function(){ lookupRef(this.value, 'preencollage_designation'); });
if (document.getElementById('ref_impression')) document.getElementById('ref_impression').addEventListener('blur', function(){ lookupRef(this.value, 'impression_designation'); });
if (document.getElementById('ref_impression_2')) document.getElementById('ref_impression_2').addEventListener('blur', function(){ lookupRef(this.value, 'impression2_designation'); });
if (document.getElementById('ref_impression_3')) document.getElementById('ref_impression_3').addEventListener('blur', function(){ lookupRef(this.value, 'impression3_designation'); });
</script>

<?php require_once __DIR__ . '/footer_simple.php'; ?>
