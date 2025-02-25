<?php
require 'header.php';

// Funcție pentru a citi fișierul CSV și a returna datele sub formă de array
function citesteCSV($nume_fisier) {
    $date = [];
    if (($handle = fopen($nume_fisier, "r")) !== FALSE) {
        $header = fgetcsv($handle, 1000, ","); // Citim antetul
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $date[] = array_combine($header, $row);
        }
        fclose($handle);
    }
    return $date;
}

// Citim datele din fișierul meniu.csv
$produse = citesteCSV('meniu.csv');

// Grupăm produsele pe categorii și stocăm imaginile acestora
$categorii = [];
foreach ($produse as $produs) {
    $categorie = $produs['Categorie'];
    
    // Adăugăm categoria dacă nu există deja
    if (!isset($categorii[$categorie])) {
        $categorii[$categorie] = [
            'nume' => $categorie,
            'imagine' => $produs['Imagine Categorie'] ?? 'default.jpg', // Imagine categorie din CSV sau implicită
            'produse' => []
        ];
    }
    
    // Adăugăm produsul în lista categoriei
    $categorii[$categorie]['produse'][] = $produs;
}

?>


<div class="my-text"><h1>Restaurant Capricii</h1></div>

<div class="header-buttons">
    <a href="https://restaurant-capricii.ro" class="btn-header" target="_blank"><i class="fa-solid fa-globe"></i></a>
    <a href="tel:+40230706060" class="btn-header"><i class="fa-solid fa-phone"></i></a>
    <a href="https://maps.google.com/?q=Restaurant+Capricii+Radauti" class="btn-header" target="_blank"><i class="fa-solid fa-location-dot"></i></a>
    <a href="#" class="btn-header" onclick="afiseazaWiFi()"><i class="fa-solid fa-wifi"></i></a>
</div>


<?php foreach ($categorii as $infoCategorie): ?>
    <div class="categorie" onclick="toggleCategorie('<?php echo md5($infoCategorie['nume']); ?>')">
        <img src="<?php echo htmlspecialchars($infoCategorie['imagine']); ?>" alt="Imagine categorie" class="img-categorie">
        <h2><?php echo htmlspecialchars($infoCategorie['nume']); ?></h2>
    </div>
     <img src="header.png" height="20" style="display: block; margin: 10px auto; vertical-align: bottom;">
    <div id="<?php echo md5($infoCategorie['nume']); ?>" class="produse">
        <?php foreach ($infoCategorie['produse'] as $produs): ?>
            <div class="produs" onclick="location.href='produs.php?id=<?php echo $produs['ID']; ?>'">
                <img src="<?php echo htmlspecialchars($produs['Imagine URL']); ?>" alt="Imagine produs">
                <div class="produs-detalii">
                    <h3><?php echo htmlspecialchars($produs['Nume Produs']); ?></h3>
                    <p><strong>Cantitate:</strong> <?php echo htmlspecialchars($produs['Cantitate']); ?></p>
                    <p><strong>Preț:</strong> <?php echo htmlspecialchars($produs['Pret']); ?> RON</p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<div class="header-buttons">
    <a href="https://restaurant-capricii.ro" class="btn-header" target="_blank"><i class="fa-solid fa-globe"></i></a>
    <a href="tel:+40230706060" class="btn-header"><i class="fa-solid fa-phone"></i></a>
    <a href="https://maps.google.com/?q=Restaurant+Capricii+Radauti" class="btn-header" target="_blank"><i class="fa-solid fa-location-dot"></i></a>
    <a href="#" class="btn-header" onclick="afiseazaWiFi()"><i class="fa-solid fa-wifi"></i></a>
</div>


<script>
function toggleCategorie(id) {
    let div = document.getElementById(id);
    div.classList.toggle("active");
}
</script>

<script>
function afiseazaWiFi() {
    alert("📶 Conectare WiFi\n\n🔹 Nume rețea: NumeleRetelei\n🔑 Parolă: ParolaWiFi");
}
</script>

</body>
</html>
