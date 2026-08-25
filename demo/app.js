const DATA_URL = "data/banks.json";
const PHONE_DATA_URL = "data/phone-resolution.json";

const state = {
    banks: [],
    phoneRules: new Map(),
};

const form = document.querySelector("#resolver-form");
const input = document.querySelector("#account-input");
const dataStatus = document.querySelector("#data-status");
const metricRecords = document.querySelector("#metric-records");
const inputError = document.querySelector("#input-error");
const resultPanel = document.querySelector("#result-panel");
const resultEyebrow = document.querySelector("#result-eyebrow");
const resultTitle = document.querySelector("#result-title");
const resultCount = document.querySelector("#result-count");
const resultDescription = document.querySelector("#result-description");
const candidateList = document.querySelector("#candidate-list");

/** Loads only repository-local JSON so the demo remains database-free and offline-first. */
async function loadData() {
    const [banksResponse, phoneResponse] = await Promise.all([
        fetch(DATA_URL),
        fetch(PHONE_DATA_URL),
    ]);

    if (!banksResponse.ok || !phoneResponse.ok) {
        throw new Error("Local resolver data could not be loaded.");
    }

    const banks = await banksResponse.json();
    const phoneData = await phoneResponse.json();

    if (!Array.isArray(banks) || !Array.isArray(phoneData.records)) {
        throw new Error("Local resolver data has an invalid shape.");
    }

    state.banks = banks;
    state.phoneRules = new Map(
        phoneData.records.map((record) => [record.slug, record]),
    );

    dataStatus.textContent = `${banks.length} local records ready`;
    dataStatus.classList.add("ready");
    metricRecords.textContent = banks.length.toLocaleString();
}

/** Removes formatting while preserving only decimal digits accepted by the resolver. */
function normalizeDigits(value) {
    return String(value).replace(/\D/g, "");
}

/** Classifies only an 11-digit Nigerian national mobile form as an unambiguous phone input. */
function isNationalPhone(value) {
    return /^0[789][01]\d{8}$/.test(value);
}

/** Converts a valid Nigerian national phone number to its normalized ten-digit representation. */
function normalizePhone(value) {
    if (isNationalPhone(value)) {
        return value.slice(1);
    }

    if (/^[789][01]\d{8}$/.test(value)) {
        return value;
    }

    return null;
}

/** Applies the NIBSS twelve-weight NUBAN checksum to one ten-digit account candidate. */
function passesNubanChecksum(accountNumber, prefix) {
    if (!/^\d{10}$/.test(accountNumber) || !/^\d{3}$/.test(prefix)) {
        return false;
    }

    const candidate = `${prefix}${accountNumber.slice(0, 9)}`;
    const weights = [3, 7, 3, 3, 7, 3, 3, 7, 3, 3, 7, 3];
    const total = candidate
        .split("")
        .reduce((sum, digit, index) => sum + Number(digit) * weights[index], 0);
    const checkDigit = (10 - (total % 10)) % 10;

    return checkDigit === Number(accountNumber[9]);
}

/** Returns unique checksum-compatible banks while ignoring records without verified three-digit prefixes. */
function findNubanCandidates(accountNumber) {
    const candidates = state.banks.filter((bank) => (
        typeof bank.prefix === "string"
        && /^\d{3}$/.test(bank.prefix)
        && passesNubanChecksum(accountNumber, bank.prefix)
    ));
    const unique = new Map(candidates.map((bank) => [bank.slug, bank]));

    return [...unique.values()];
}

/** Returns only phone-capable institutions explicitly present in the local evidence ledger. */
function findPhoneCandidates(value) {
    const normalized = normalizePhone(value);

    if (!normalized) {
        return [];
    }

    return state.banks.filter((bank) => {
        const rule = state.phoneRules.get(bank.slug);
        return rule && rule.phone_account_capability === "documented";
    });
}

/** Creates a transparent result state instead of treating a checksum collision as bank identification. */
function resolve(value) {
    const digits = normalizeDigits(value);

    if (digits.length !== 10 && digits.length !== 11) {
        return {
            status: "invalid",
            candidates: [],
            description: "Enter exactly 10 digits for an account or normalized phone number, or 11 digits for a Nigerian national phone number.",
        };
    }

    if (digits.length === 11 && isNationalPhone(digits)) {
        const candidates = findPhoneCandidates(digits);
        return {
            status: candidates.length ? "phone" : "not_found",
            candidates,
            description: candidates.length
                ? "This is a phone-shaped input. The institutions below are documented to support phone-based account interactions; the number does not identify one provider by itself."
                : "No locally documented phone-account institution matches this input.",
        };
    }

    const nubanCandidates = findNubanCandidates(digits);
    const phoneCandidates = findPhoneCandidates(digits);
    const candidates = [...new Map(
        [...nubanCandidates, ...phoneCandidates].map((bank) => [bank.slug, bank]),
    ).values()];

    return {
        status: candidates.length === 1 ? "single_candidate" : candidates.length ? "ambiguous" : "not_found",
        candidates,
        description: candidates.length === 1
            ? "One locally configured candidate matched. This is still checksum evidence, not a live account-name confirmation."
            : candidates.length
                ? "Multiple local rules match this value. The resolver will not choose a bank automatically."
                : "No locally configured candidate matched this value.",
    };
}

/** Escapes user-controlled text before it is inserted into the result list. */
function escapeHtml(value) {
    return String(value)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

/** Renders each candidate with a local logo fallback and no external asset requests. */
function renderCandidates(candidates) {
    candidateList.innerHTML = candidates.map((bank) => {
        const logoPath = bank.logo_path;
        return `
            <article class="candidate">
                <img src="${escapeHtml(logoPath)}" alt="" width="48" height="48" loading="lazy" onerror="this.hidden=true">
                <div>
                    <h3>${escapeHtml(bank.name.trim())}</h3>
                    <p>Code: ${escapeHtml(bank.code)} · Prefix: ${escapeHtml(bank.prefix ?? "Not verified")}</p>
                </div>
            </article>
        `;
    }).join("");
}

/** Updates the visible result panel with the resolver state and candidate count. */
function renderResult(result) {
    resultPanel.hidden = false;
    resultEyebrow.textContent = result.status.replaceAll("_", " ");
    resultTitle.textContent = result.status === "ambiguous"
        ? "Ambiguous result"
        : result.status === "single_candidate"
            ? "One local candidate"
            : result.status === "phone"
                ? "Documented phone-account candidates"
                : result.status === "invalid"
                    ? "Invalid input"
                    : "No local match";
    resultCount.textContent = result.candidates.length
        ? `${result.candidates.length} candidate${result.candidates.length === 1 ? "" : "s"}`
        : "";
    resultDescription.textContent = result.description;
    renderCandidates(result.candidates);
}

/** Displays validation errors without submitting malformed input to the resolver. */
function showError(message) {
    inputError.textContent = message;
    inputError.hidden = !message;
}

document.querySelectorAll("[data-example]").forEach((button) => {
    button.addEventListener("click", () => {
        input.value = button.dataset.example ?? "";
        input.focus();
    });
});

form.addEventListener("submit", (event) => {
    event.preventDefault();
    showError("");

    try {
        renderResult(resolve(input.value));
    } catch (error) {
        showError(error instanceof Error ? error.message : "The resolver could not process this input.");
    }
});

loadData().catch((error) => {
    dataStatus.textContent = "Local data unavailable";
    dataStatus.classList.add("error-pill");
    showError(error instanceof Error ? error.message : "Local resolver data could not be loaded.");
});
