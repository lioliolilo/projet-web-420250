document.addEventListener("DOMContentLoaded", () => {
    const selectService = document.getElementById("service");

    const creneauxBox = document.getElementById("creneauxContainer");
    const creneauxList = document.getElementById("creneauxList");

    selectService.addEventListener("change", function () {
        const option = this.options[this.selectedIndex];
        const serviceId = this.value;

        // reset des créneaux
        creneauxBox.style.display = "none";
        creneauxList.innerHTML = "";

        if (!serviceId) return;

        // charger les créneaux via AJAX
        fetch("api/get_creneaux.php?service=" + serviceId)
            .then(r => r.json())
            .then(data => {
                creneauxList.innerHTML = "";

                data.forEach(item => {
                    const div = document.createElement("div");
                    div.className = "tuteur-creneaux";

                    div.innerHTML += `<p><strong>${item.tuteur}</strong></p>`;

                    item.creneaux.forEach(h => {
                        const btn = document.createElement("button");
                        btn.className = "btn-creneau";
                        btn.textContent = h + ":00";
                        btn.dataset.tuteur = item.id_tuteur;
                        btn.dataset.heure = h;

                        btn.addEventListener("click", () => {
                            document.querySelectorAll(".btn-creneau").forEach(b => b.classList.remove("selected"));
                            btn.classList.add("selected");
                            alert("Créneau sélectionné : " + h + ":00 avec " + item.tuteur);
                        });

                        div.appendChild(btn);
                    });

                    creneauxList.appendChild(div);
                });

                creneauxBox.style.display = "block";
            });
    });
});
