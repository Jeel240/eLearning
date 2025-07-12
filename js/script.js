document.addEventListener("DOMContentLoaded", () => {
    const counters = document.querySelectorAll(".counter");
    counters.forEach(counter => {
        counter.innerText = "0";
        const updateCounter = () => {
            const target = +counter.getAttribute("data-target");
            const current = +counter.innerText;
            const increment = target / 100;
            if (current < target) {
                counter.innerText = Math.ceil(current + increment);
                setTimeout(updateCounter, 50);
            } else {
                counter.innerText = target;
            }
        };
        updateCounter();
    });
});

// Show pop-up on page load
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("popupBox").style.display = "block";
    document.getElementById("overlay").style.display = "block";
});

// Close pop-up
function closePopup() {
    document.getElementById("popupBox").style.display = "none";
    document.getElementById("overlay").style.display = "none";
}

function toggleMenu() {
    var menu = document.getElementById("menu");
    if (menu.style.display === "block") {
        menu.style.display = "none";
    } else {
        menu.style.display = "block";
    }
}

function toggleSidebar() {
    var sidebar = document.getElementById("sidebar");
    if (sidebar.style.width === "250px" || sidebar.style.width === "") {
        sidebar.style.width = "0";
    } else {
        sidebar.style.width = "250px";
    }
}
