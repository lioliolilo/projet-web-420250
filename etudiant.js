document.addEventListener("DOMContentLoaded", () => {
    const selectService = document.getElementById("service");
    const infoBox = document.getElementById("tuteur-info");
    const nomTuteur = document.getElementById("nomTuteur");

    selectService.addEventListener("change", function () {
        const option = this.options[this.selectedIndex];
        const tuteurs = option.getAttribute("data-tuteurs");

        if (!tuteurs || tuteurs.trim() === "") {
            nomTuteur.innerHTML = "<em>Aucun tuteur disponible</em>";
            infoBox.style.display = "block";
            return;
        }

        const list = tuteurs.split("|").map(t => t.trim());

        nomTuteur.innerHTML = list.map(t => `<div>${t}</div>`).join("");

        infoBox.style.display = "block";
    });
});
