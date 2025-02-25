<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// === CONFIGURARE PAROLĂ ===
$parola_corecta = "admin123"; // 🔒 Schimbă parola aici!

// === LOGOUT ===
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: editare.php");
    exit;
}

// === VERIFICARE AUTENTIFICARE ===
if (!isset($_SESSION['autentificat'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['parola'])) {
        if ($_POST['parola'] === $parola_corecta) {
            $_SESSION['autentificat'] = true;
            header("Location: editare.php");
            exit;
        } else {
            echo "<p style='color:red; text-align:center;'>❌ Parolă incorectă!</p>";
        }
    }

    echo '<form method="POST" style="text-align:center; margin-top:50px;">
            <h2>🔒 Acces restricționat</h2>
            <input type="password" name="parola" placeholder="Introdu parola" required>
            <button type="submit">Autentificare</button>
        </form>';
    exit;
}

// === FUNCȚII PENTRU CSV ===
function citesteCSV($fisier) {
    $date = [];
    if (($handle = fopen($fisier, "r")) !== FALSE) {
        $header = fgetcsv($handle, 1000, ",");
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($row) === count($header)) { // ✅ Verifică dacă rândul are același număr de coloane
                $date[] = array_combine($header, $row);
            } else {
                error_log("⚠️ Rând invalid în `$fisier`: " . implode(", ", $row));
            }
        }
        fclose($handle);
    }
    return $date;
}


function scrieCSV($fisier, $date) {
    $handle = fopen($fisier, "w");
    fputcsv($handle, array_keys($date[0]));
    foreach ($date as $linie) {
        fputcsv($handle, $linie);
    }
    fclose($handle);
}

// === ÎNCĂRCARE DATE ===
$produse = citesteCSV("meniu.csv");

// === SALVARE MODIFICĂRI ===
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $produse_noi = [];

    foreach ($_POST['id'] as $index => $id) {
        if (!isset($_POST['sterge'][$id])) { // Dacă produsul nu e bifat pentru ștergere
            $produse_noi[] = [
                'ID' => $id ?: uniqid(),
                'Categorie' => $_POST['categorie'][$index],
                'Nume Produs' => $_POST['nume'][$index],
                'Cantitate' => $_POST['cantitate'][$index],
                'Pret' => $_POST['pret'][$index],
                'Informatii' => $_POST['info'][$index],
                'Valori Nutritionale' => $_POST['valori'][$index],
                'Alergeni' => $_POST['alergeni'][$index],
                'Imagine URL' => $_POST['imagine'][$index]
            ];
        }
    }
    scrieCSV("meniu.csv", $produse_noi);
    header("Location: editare.php");
    exit;
}

// === GRUPARE PE CATEGORII ===
$categorii = [];
foreach ($produse as $produs) {
    $categorii[$produs['Categorie']][] = $produs;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Editare Meniu</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        input { width: 100%; padding: 5px; }
        .logout { text-align: right; margin-bottom: 10px; }
        .logout a { text-decoration: none; color: red; }
        .button-group { margin-bottom: 15px; }
        .button-group button { margin-right: 10px; padding: 5px 10px; cursor: pointer; }
    </style>
</head>
<body>

<div class="logout">
    <a href="editare.php?logout=true">❌ Logout</a>
</div>

<h2>📋 Editare Meniu</h2>
<form method="POST" id="meniuForm">
    <?php foreach ($categorii as $categorie => $produse_categorie): ?>
        <h3>🍽️ <?php echo htmlspecialchars($categorie); ?></h3>
        <table id="tabel_<?php echo md5($categorie); ?>">
            <tr>
                <th>✅</th>
                <th>Nume Produs</th>
                <th>Cantitate</th>
                <th>Preț (RON)</th>
                <th>Informații</th>
                <th>Valori Nutriționale</th>
                <th>Alergeni</th>
                <th>Imagine URL</th>
            </tr>

            <?php foreach ($produse_categorie as $produs): ?>
                <tr>
                    <td><input type="checkbox" name="sterge[<?php echo $produs['ID']; ?>]"></td>
                    <td><input type="hidden" name="id[]" value="<?php echo $produs['ID']; ?>">
                        <input type="hidden" name="categorie[]" value="<?php echo htmlspecialchars($categorie); ?>">
                        <input type="text" name="nume[]" value="<?php echo htmlspecialchars($produs['Nume Produs']); ?>">
                    </td>
                    <td><input type="text" name="cantitate[]" value="<?php echo htmlspecialchars($produs['Cantitate']); ?>"></td>
                    <td><input type="text" name="pret[]" value="<?php echo htmlspecialchars($produs['Pret']); ?>"></td>
                    <td><input type="text" name="info[]" value="<?php echo htmlspecialchars($produs['Informatii']); ?>"></td>
                    <td><input type="text" name="valori[]" value="<?php echo htmlspecialchars($produs['Valori Nutritionale']); ?>"></td>
                    <td><input type="text" name="alergeni[]" value="<?php echo htmlspecialchars($produs['Alergeni']); ?>"></td>
                    <td><input type="text" name="imagine[]" value="<?php echo htmlspecialchars($produs['Imagine URL']); ?>"></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="button-group">
            <button type="button" onclick="adaugaProdus('<?php echo md5($categorie); ?>', '<?php echo $categorie; ?>')">➕ Adaugă produs</button>
            <button type="submit">🗑️ Șterge produse selectate</button>
        </div>
    <?php endforeach; ?>

    <button type="submit" name="salveaza">💾 Salvează modificările</button>
</form>

<script>
function adaugaProdus(tabelId, categorie) {
    let tabela = document.getElementById('tabel_' + tabelId);
    let rand = `<tr>
        <td><input type="checkbox" name="sterge[]"></td>
        <td><input type="hidden" name="id[]" value=""><input type="hidden" name="categorie[]" value="${categorie}"><input type="text" name="nume[]" placeholder="Nume produs"></td>
        <td><input type="text" name="cantitate[]" placeholder="Cantitate"></td>
        <td><input type="text" name="pret[]" placeholder="Preț"></td>
        <td><input type="text" name="info[]" placeholder="Informații"></td>
        <td><input type="text" name="valori[]" placeholder="Valori Nutriționale"></td>
        <td><input type="text" name="alergeni[]" placeholder="Alergeni"></td>
        <td><input type="text" name="imagine[]" placeholder="Imagine URL"></td>
    </tr>`;
    tabela.innerHTML += rand;
}
</script>

</body>
</html>
