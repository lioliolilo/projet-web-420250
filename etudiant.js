document.addEventListener("DOMContentLoaded", () => {
    const selectService = document.getElementById("service");
    const infoBox = document.getElementById("tuteur-info");

    const nomTuteur = document.getElementById("nomTuteur");
    const specTuteur = document.getElementById("specTuteur");

    selectService.addEventListener("change", function() {
        const option = this.options[this.selectedIndex];

        const tuteurs = option.getAttribute("data-tuteurs");
        const specs = option.getAttribute("data-specialites");

        if (!tuteurs || tuteurs.trim() === "") {
            infoBox.style.display = "none";
            return;
        }

        nomTuteur.textContent = tuteurs.replace(/\|/g, ", ");
        specTuteur.textContent = specs.replace(/\|/g, ", ");

        infoBox.style.display = "block";
    });
});
