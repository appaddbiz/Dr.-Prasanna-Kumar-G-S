document.addEventListener("DOMContentLoaded", function () {
    const counterElement = document.getElementById("footerVisitorCount");

    if (!counterElement) {
        return;
    }

    fetch("/visitor-counter.php", {
        method: "GET",
        cache: "no-store",
        credentials: "same-origin"
    })
    .then(function (response) {
        if (!response.ok) {
            throw new Error("Unable to load visitor counter");
        }

        return response.json();
    })
    .then(function (data) {
        if (!data.success) {
            throw new Error(data.message || "Visitor counter error");
        }

        counterElement.textContent =
            Number(data.count).toLocaleString("en-IN");
    })
    .catch(function (error) {
        console.error("Visitor counter error:", error);
        counterElement.textContent = "0";
    });
});