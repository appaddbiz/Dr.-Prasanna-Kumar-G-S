document.addEventListener("DOMContentLoaded", function () {
    const visitorCounter = document.getElementById("footerVisitorCount");

    if (!visitorCounter) {
        console.error("Element #footerVisitorCount not found.");
        return;
    }

    fetch("/visitor-counter.php?time=" + Date.now(), {
        method: "GET",
        cache: "no-store",
        credentials: "same-origin"
    })
        .then(function (response) {
            if (!response.ok) {
                return response.text().then(function (message) {
                    throw new Error(
                        "PHP error " + response.status + ": " + message
                    );
                });
            }

            return response.json();
        })
        .then(function (data) {
            console.log("Visitor counter response:", data);

            if (data.success && data.count !== undefined) {
                visitorCounter.textContent =
                    Number(data.count).toLocaleString("en-IN");
            } else {
                throw new Error(data.message || "Invalid PHP response");
            }
        })
        .catch(function (error) {
            console.error("Visitor counter failed:", error);
        });
});