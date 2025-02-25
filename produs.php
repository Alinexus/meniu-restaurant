<?php
require 'header.php';
// Funcție pentru a citi fișierul CSV
function citesteCSV($nume_fisier) {
    $date = [];
    if (($handle = fopen($nume_fisier, "r")) !== FALSE) {
        $header = fgetcsv($handle, 1000, ",");
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $date[] = array_combine($header, $row);
        }
        fclose($handle);
    }
    return $date;
}

// Citim produsele
$produse = citesteCSV('meniu.csv');

// Căutăm produsul după ID
$produs = null;
if (isset($_GET['id'])) {
    foreach ($produse as $p) {
        if ($p['ID'] == $_GET['id']) {
            $produs = $p;
            break;
        }
    }
}

if (!$produs) {
    echo "Produsul nu a fost găsit!";
    exit;
}
?>
<div class="produs-imagine">
            <img src="<?php echo htmlspecialchars($produs['Imagine URL']); ?>" alt="Imagine produs">
        </div>
    <div class="produs-container">
        <div class="produs-detalii">
            <img src="header.png" height="20" style="display: block; margin: 10px auto; vertical-align: bottom;">
            <h1><?php echo htmlspecialchars($produs['Nume Produs']); ?></h1>
            <p><strong>Gramaj:</strong> <?php echo htmlspecialchars($produs['Cantitate']); ?></p>
            <p><strong>Pret:</strong> <?php echo htmlspecialchars($produs['Pret']); ?> RON</p>
            <p><strong>Continut Produs:</strong><br> <?php echo nl2br(htmlspecialchars($produs['Informatii'])); ?></p>

            <h3>Valori nutriționale / 100g:</h3>
            <p><?php echo nl2br(htmlspecialchars($produs['Valori Nutritionale'])); ?></p>

            <h3>Alergeni:</h3>
            <p><?php echo htmlspecialchars($produs['Alergeni'] ?? 'Nespecificat'); ?></p>

            <div class="categorie" onclick="window.location.href='index.php'">
    <h2>Înapoi la meniu</h2>
</div>
<img src="header.png" height="20" style="display: block; margin: 10px auto; vertical-align: bottom;">
        </div>
    </div>
    

</body>
</html>
