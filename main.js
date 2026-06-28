// public/js/main.js

document.addEventListener("DOMContentLoaded", () => {
    
    // ==========================================
    // 1. GESTION DU MINUTEUR (10 MINUTES)
    // ==========================================
    const timerElement = document.getElementById("timer");
    const quizForm = document.getElementById("quiz-form");
    
    let tempsRestant = 10 * 60; // 10 minutes converties en secondes (600s)
    let avertissementsTriche = 0;

    const mettreAJourChrono = () => {
        let minutes = Math.floor(tempsRestant / 60);
        let secondes = tempsRestant % 60;

        // Ajouter un 0 devant si le chiffre est inférieur à 10 (ex: 09:05)
        minutes = minutes < 10 ? "0" + minutes : minutes;
        secondes = secondes < 10 ? "0" + secondes : secondes;

        timerElement.textContent = `${minutes}:${secondes}`;

        // Alerte visuelle s'il reste moins d'une minute (Optionnel - Style CSS)
        if (tempsRestant <= 60) {
            timerElement.style.color = "#ff4d4d";
            timerElement.style.fontWeight = "bold";
        }

        // Si le temps est écoulé
        if (tempsRestant <= 0) {
            clearInterval(chronoInterval);
            alert("Temps écoulé ! Votre test va être soumis automatiquement.");
            quizForm.submit(); // Envoi forcé du formulaire au serveur
        }

        tempsRestant--;
    };

    // Lancer le chrono toutes les secondes
    const chronoInterval = setInterval(mettreAJourChrono, 1000);


    // ==========================================
    // 2. SYSTEME ANTI-TRICHE (CHANGEMENT D'ONGLET)
    // ==========================================
    document.addEventListener("visibilitychange", () => {
        // Si la page devient cachée (l'utilisateur change d'onglet ou quitte le navigateur)
        if (document.hidden) {
            avertissementsTriche++;

            if (avertissementsTriche === 1) {
                alert("⚠️ ATTENTION : Le changement d'onglet ou d'application est interdit pendant le test de QI. Au prochain écart, votre test sera annulé.");
            } else if (avertissementsTriche >= 2) {
                clearInterval(chronoInterval);
                
                // On peut ajouter un champ caché dynamique pour signaler l'abandon/triche au serveur
                const inputTriche = document.createElement("input");
                inputTriche.type = "hidden";
                inputTriche.name = "triche_detectee";
                inputTriche.value = "1";
                quizForm.appendChild(inputTriche);

                alert("❌ TRICHE DÉTECTÉE : Vous avez quitté la page une seconde fois. Soumission immédiate de votre test.");
                quizForm.submit();
            }
        }
    });

    // En cas de soumission manuelle, on nettoie l'intervalle pour éviter les fuites de mémoire
    quizForm.addEventListener("submit", () => {
        clearInterval(chronoInterval);
    });
});