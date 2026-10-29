
const faqList = document.getElementById("faqList");
const faqSearch = document.getElementById("faqSearch");
const faqCategory = document.getElementById("faqCategory");
const faqSort = document.getElementById("faqSort");
const faqMessage = document.getElementById("faqMessage");

const faqPrev = document.getElementById("faqPrev");
const faqNext = document.getElementById("faqNext");
const faqPageInfo = document.getElementById("faqPageInfo");

let faqs = Array.isArray(window.STUDENTHUB_FAQS) ? window.STUDENTHUB_FAQS : [];
let currentPage = 1;
const itemsPerPage = 8;

function textOf(value) {
    return String(value ?? "");
}

function displayFAQs() {
    const searchText = faqSearch.value.toLowerCase().trim();
    const selectedCategory = faqCategory.value;
    const sortOrder = faqSort.value;

    let filteredFAQs = faqs.filter(faq => {
        const question = textOf(faq.question).toLowerCase();
        const answer = textOf(faq.answer).toLowerCase();
        const matchesSearch = question.includes(searchText) || answer.includes(searchText);
        const matchesCategory =
            selectedCategory === "All" || faq.category === selectedCategory;

        return matchesSearch && matchesCategory;
    });

    filteredFAQs.sort((a, b) => {
        if (sortOrder === "newest") {
            return (Number(b.id) || 0) - (Number(a.id) || 0);
        }

        const result = textOf(a.question).localeCompare(textOf(b.question));
        return sortOrder === "az" ? result : -result;
    });

    const totalPages = Math.ceil(filteredFAQs.length / itemsPerPage);

    if (currentPage > totalPages) {
        currentPage = Math.max(1, totalPages);
    }

    const startIndex = (currentPage - 1) * itemsPerPage;
    const currentFAQs = filteredFAQs.slice(startIndex, startIndex + itemsPerPage);

    faqList.replaceChildren();

    if (filteredFAQs.length === 0) {
        faqMessage.textContent = "No FAQs found.";
    } else {
        faqMessage.textContent = "Total FAQs found: " + filteredFAQs.length;
    }

    currentFAQs.forEach(faq => {
        const details = document.createElement("details");

        const summary = document.createElement("summary");
        summary.textContent = textOf(faq.question);

        const answer = document.createElement("p");
        answer.textContent = textOf(faq.answer);

        const category = document.createElement("small");
        category.className = "faq-category";
        category.textContent = textOf(faq.category);

        details.appendChild(summary);
        details.appendChild(answer);
        details.appendChild(category);

        faqList.appendChild(details);
    });

    faqPageInfo.textContent =
        "Page " + (totalPages ? currentPage : 0) + " of " + totalPages;

    faqPrev.disabled = currentPage <= 1;
    faqNext.disabled = totalPages === 0 || currentPage >= totalPages;
}

function loadFaqsFromFile() {
    return fetch("faq.json?t=" + Date.now(), { cache: "no-store" })
        .then(response => {
            if (!response.ok) {
                throw new Error("Could not load FAQ data");
            }
            return response.json();
        })
        .then(data => {
            faqs = Array.isArray(data) ? data : [];
            displayFAQs();
        });
}

if (faqs.length) {
    displayFAQs();
} else {
    loadFaqsFromFile().catch(error => {
        faqMessage.textContent = "Unable to load FAQs. Please check faq.json.";
        console.error(error);
    });
}

faqSearch.addEventListener("input", () => {
    currentPage = 1;
    displayFAQs();
});

faqCategory.addEventListener("change", () => {
    currentPage = 1;
    displayFAQs();
});

faqSort.addEventListener("change", () => {
    currentPage = 1;
    displayFAQs();
});

faqPrev.addEventListener("click", () => {
    if (currentPage > 1) {
        currentPage--;
        displayFAQs();
    }
});

faqNext.addEventListener("click", () => {
    currentPage++;
    displayFAQs();
});
