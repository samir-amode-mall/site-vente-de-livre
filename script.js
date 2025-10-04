function recherche_auteurs() {
    let search = document.getElementById("searchAuteur").value;
    let resultDiv = document.getElementById("resultatsAuteurs");

    if (search.trim() === "") {
        resultDiv.innerHTML = "";
        return;
    }

    if (search.length > 1) {
        let xhr = new XMLHttpRequest();
        xhr.open("GET", "recherche_auteurs.php?debnom=" + encodeURIComponent(search), true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                let auteurs = JSON.parse(xhr.responseText);
                resultDiv.innerHTML = "<ul>";

                auteurs.forEach(auteur => {
                    resultDiv.innerHTML += `<li>
                        <a href="#" onclick="recherche_ouvrages_auteur(${auteur.code})">
                            ${auteur.nom} ${auteur.prenom}
                        </a>
                    </li>`;
                });

                resultDiv.innerHTML += "</ul>";
            }
        };
        xhr.send();
    }
}




function recherche_ouvrages_titre() {
    let search = document.getElementById("searchLivre").value;
    let resultDiv = document.getElementById("resultatsLivres");

    if (search.trim() === "") {
        resultDiv.innerHTML = "";
        return;
    }

    if (search.length > 1) {
        let xhr = new XMLHttpRequest();
        xhr.open("GET", "recherche_ouvrages_titre.php?debtitre=" + encodeURIComponent(search), true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                console.log("Réponse reçue:", xhr.responseText);

                if (xhr.status === 200) {
                    let ouvrages = JSON.parse(xhr.responseText);
                    affiche_ouvrages(ouvrages);
                } else {
                    console.error("Erreur AJAX :", xhr.status, xhr.statusText);
                }
            }
        };
        xhr.send();
    }
}

function recherche_ouvrages_auteur(code) {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "recherche_ouvrages_auteur.php?code=" + encodeURIComponent(code), true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            console.log("Réponse reçue:", xhr.responseText);

            if (xhr.status === 200) {
                let ouvrages = JSON.parse(xhr.responseText);
                affiche_ouvrages(ouvrages);
            } else {
                console.error("Erreur AJAX :", xhr.status, xhr.statusText);
            }
        }
    };
    xhr.send();
}


function affiche_ouvrages(ouvrages) {
    let resultDiv = document.getElementById("resultatsLivres");

    if (!resultDiv) {
        console.error("Erreur : L'élément #resultatsLivres est introuvable.");
        return;
    }

    resultDiv.innerHTML = "<ol>";

    ouvrages.forEach(ouvrage => {
        resultDiv.innerHTML += `
            <li onclick="affiche_exemplaires(${ouvrage.code})">
                ${ouvrage.nom}
                <ul id="exemplaires-${ouvrage.code}" style="display: none;"></ul>
            </li>`;
    });

    resultDiv.innerHTML += "</ol>";
}


function affiche_exemplaires(code_ouvrage) {
    let ul = document.getElementById("exemplaires-" + code_ouvrage);

    if (ul.style.display === "none") {
        ul.style.display = "block";
    } else {
        ul.style.display = "none";
        return;
    }

    ul.innerHTML = "";

    let xhr = new XMLHttpRequest();
    xhr.open("GET", "recherche_ouvrages_titre.php?debtitre=" + encodeURIComponent(document.getElementById("searchLivre").value), true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let ouvrages = JSON.parse(xhr.responseText);
            let ouvrage = ouvrages.find(o => o.code == code_ouvrage);

            if (ouvrage && ouvrage.exemplaires.length > 0) {
                ouvrage.exemplaires.forEach(exemplaire => {
                    ul.innerHTML += `<li>
                        ${exemplaire.editeur_nom}, ${exemplaire.prix}€
                        <button onclick="ajouter_panier('${exemplaire.code}')">🛒 Ajouter</button>
                    </li>`;

                });
            } else {
                ul.innerHTML = "<li>Aucun exemplaire disponible</li>";
            }
        }
    };
    xhr.send();
}

function ajouter_panier(code_exemplaire) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajouter_panier.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert("L'article a été ajouté au panier");
            afficher_panier();
        }
    };

    xhr.send("code_exemplaire=" + encodeURIComponent(code_exemplaire));
}

function afficher_panier() {
    let xhr = new XMLHttpRequest();
    let cart = document.getElementById("cart");
    let mainContent = document.getElementById("main-content");

    cart.classList.add("show");
    mainContent.style.marginRight = "300px";

    xhr.open("GET", "afficher_panier.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById("contenu-panier").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}

function fermer_panier() {
    let cart = document.getElementById("cart");
    let mainContent = document.getElementById("main-content");

    cart.classList.remove("show");
    mainContent.style.marginRight = "0";
}

function vider_panier() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "vider_panier.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert(xhr.responseText);
            afficher_panier();
        }
    };

    xhr.send();
}


function commander() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "commander.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert(xhr.responseText);
            afficher_panier();
        }
    };

    xhr.send();
}


function enregistrement() {
    let form = document.getElementById("inscriptionForm");
    let formData = new FormData(form);

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "inscription.php", true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            let message = document.getElementById("message");

            if (xhr.status === 200) {
                let response = xhr.responseText.trim();

                if (response === "missing_fields") {
                    message.innerText = "Veuillez remplir tous les champs.";
                } else if (response === "duplicate") {
                    message.innerText = "Cet utilisateur existe déjà.";
                } else if (!isNaN(response)) {
                    document.cookie = "client=" + response + "; expires=Fri, 31 Dec 2050 23:59:59 UTC; path=/";
                    window.location.href = "index.php";
                } else {
                    message.innerText = "Erreur inconnue.";
                }
            } else {
                message.innerText = "Erreur lors de l'inscription.";
            }
        }
    };

    xhr.send(formData);
}


document.getElementById("searchAuteur").addEventListener("keyup", recherche_auteurs);
document.getElementById("searchLivre").addEventListener("keyup", recherche_ouvrages_titre);

document.getElementById("toggleInscription").addEventListener("click", function() {
    let form = document.getElementById("formInscription");
    form.style.display = (form.style.display === "none" || form.style.display === "") ? "block" : "none";
});
