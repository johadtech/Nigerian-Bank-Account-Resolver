# Research Notes

## 2026-08-23

The official Central Bank of Nigeria Financial Institutions page states that CBN supervises twelve categories: Bureaux-de-Change, Commercial Banks, Development Finance Institutions, Discount Houses, Finance Companies, Holding Companies, Merchant Banks, Microfinance Banks, Mobile Money Operators, Non-Interest Banks, Payment Service Banks, and Primary Mortgage Banks. Source: https://www.cbn.gov.ng/supervision/finstitutions.html.

The CBN commercial-banks page is reachable at https://www.cbn.gov.ng/supervision/Inst-DM.html, but its extracted page currently exposes only the category heading and related links, not a machine-readable institution list. No commercial-bank record should be treated as newly verified from that page alone.

The existing repository dataset must not be described as verified in full. The audit found mixed code formats, non-numeric prefixes, duplicated logo fields, missing provenance, and unsupported status/account-rule claims. The new repository will therefore retain only fields backed by a cited source and will represent unavailable values as null/unknown with a verification status.

## Source policy

CBN licensing/status pages are the primary source for institution existence and category. NIBSS or an official bank/payment-network source is required for transfer identifiers where available. An institution's own official website or verified brand asset is preferred for logos. Third-party directories may be used only as leads and may not independently establish active status or an account-number rule.


## Additional CBN verification

The official CBN Micro-Finance Banks page uses a paginated Kendo table. Its rendered table exposes `aria-rowcount="830"`, while page 1 shows 20 records. This demonstrates that the earlier 280-institution scope was incomplete and that CBN's MFB universe is substantially larger. The new repository must not claim complete Nigerian coverage until all relevant CBN categories and all pagination records have been collected and verified. The table URL is https://www.cbn.gov.ng/supervision/Inst-MF.html.

The browser-rendered table currently does not expose a source date in the visible page content. The dataset should record the retrieval date separately and avoid interpreting the page as a historical “active as of” date unless CBN publishes one.


## CBN MFB table extraction

The rendered CBN MFB data source reports `total: 829` in the Kendo data source; after requesting page size 1000, it returned 829 institution names. The table's visible page had shown 20 records and an accessibility row count of 830, so the precise extracted dataset count is 829 records at retrieval time. The apparent difference may reflect a header/count convention and must be preserved as an observed discrepancy until independently resolved. This is strong evidence that the old 280-record dataset cannot be used as a complete MFB inventory.

The CBN table records exposed through the page contain institution names but no populated code, website, or licensing-date fields in the returned objects inspected. Therefore, institution names can be sourced from CBN, but codes and account-number rules require separate authoritative evidence and must not be inferred from this table.


## Reproducible CBN extraction endpoint

The official CBN MFB page's Kendo data source exposes the read endpoint `/api/GetMFBs` on the same CBN host. The data source reports `serverPaging: false` and `serverFiltering: false`; requesting page size 1000 returned 829 records. This endpoint should be used only as a source snapshot for institution metadata. It does not, by itself, establish NIP transfer codes, NUBAN prefixes, phone-account behavior, or logo rights.


## CBN Payment Service Banks

The official CBN Payment Service Banks page reports five records at retrieval time: 9 PSB Ltd, Hope PSB Ltd, MOMO PAYMENT SERVICE BANK, Moneymaster PSB Ltd, and SMARTCASH PSB LIMITED. The rendered data source exposes `/api/GetPSBs` on the CBN host and reports `total: 5`. The records inspected did not include transfer codes or account-number rules.

Source page: https://www.cbn.gov.ng/supervision/Inst-PSB.html.


## CBN Merchant Banks

The official CBN Merchant Banks page reports six records at retrieval time: CORONATION MERCHANT BANK, FBNQUEST MERCHANT BANK, FSDH MERCHANT BANK, GREENWICH MERCHANT BANK, RAND MERCHANT BANK, and Stable Microfinance Bank Limited. The rendered data source exposes `/api/GetMBs` on the CBN host and reports `total: 6`. The records inspected did not include transfer codes or account-number rules.

Source page: https://www.cbn.gov.ng/supervision/Inst-MB.html.


## CBN Commercial Banks

The official CBN commercial-bank page exposes the live data endpoint `/api/GetDMBs` on the CBN host. At retrieval time, the table reported 12 pages at 20 records per page, and the first page displayed names beginning with Access Bank Plc, Alpha Morgan Bank Limited, Citibank Nigeria Limited, Ecobank Nigeria Plc, Fidelity Bank Plc, First Bank Nigeria Limited, First City Monument Bank Plc, Globus Bank Limited, Guaranty Trust Bank Plc, Keystone Bank Limited, Nova Commercial Bank Limited, and Optimus Bank. The endpoint must be queried directly for the complete current list; the visible page is only the first page.

Source page: https://www.cbn.gov.ng/supervision/Inst-DM.html.


## CBN Mobile Money Operators

The official CBN Mobile Money Operators page exposes `/api/GetMMOs` and reports 17 listed operators at retrieval time. The visible records include Abeg Technologies Limited, Chams Mobile Limited, eTranzact International Limited, Fortis Mobile Money Limited, Funds and Electronic Transfer (FETS) Limited, Hedonmark Management Services Limited, Kongapay Technologies Limited, Mkudi Limited, NowNow Digital Systems Limited, OPay Digital Services Limited, Pagatech Limited, PalmPay Limited, Parkway Projects Limited, Teasy International Company Ltd., Visual ICT Limited, VTNetwork Limited, and Xpress MTS Limited. Several names include former-name text and line breaks; normalization must preserve the raw source name alongside a cleaned display name.

Source page: https://www.cbn.gov.ng/supervision/Inst-MMO.html.


## CBN Primary Mortgage Institutions

The official CBN Primary Mortgage Institutions page reports 12 pages at 20 records per page. The first page includes Abbey Mortgage Bank, Adamawa Mortgage Bank Limited, AG Homes, Akwa Savings, Aso Savings & Loans, Brent (Skyfield) Savings, Centage Savings & Loans, City Code, Coop Savings & Loans, Delta Mortgage Finance, FBN Mortgages, FHA Homes Ltd., First Generation Homes, Gateway Savings, Global Trust, Haggai Mortgage Bank, Home-Base Mortgage, Imperial Homes, Infinity Trust, and Jigawa Savings & Loans. The full current count and endpoint should be obtained from the rendered table's data source before inclusion.

Source page: https://www.cbn.gov.ng/supervision/Inst-PMI.html.


## CBN Primary Mortgage endpoint

The CBN Primary Mortgage Institutions table exposes `/api/GetPMIs` and reports `total: 33` with 20 records on the first page. The CBN financial-institutions index did not expose the category links in text extraction, so additional categories must be discovered from the rendered site or source code rather than assumed from historical lists.


## CBN category endpoint inventory

The official CBN index source exposed these category pages: BDC, DFI, DH, DM, FC, HC, MB, MF, MMO, NI, PMI, and PSB. Direct requests on 23 August 2026 returned valid JSON arrays for `GetDMBs` (28), `GetMBs` (6), `GetMFBs` (829), `GetMMOs` (17), `GetPSBs` (5), `GetPMIs` (33), `GetDFIs` (8), `GetDHs` (5), `GetFCs` (126), and `GetHCs` (7). The guessed `GetNIs` and `GetBDCs` endpoints returned HTTP 404 and were not retained as valid source snapshots. The repository must label these endpoint results accurately and avoid assuming that every page slug maps to an API name.


## NUBAN and NIP scope evidence

The CBN Payments System page documents implementation of the Nigeria Uniform Bank Account Number (NUBAN) and links the National Payments System standards. NIBSS describes NIP as an account-number-based, real-time electronic funds transfer platform and states that NIP supports banks, other financial institutions, fintechs, mobile-money operators, and PSSPs. These sources support the package's separation between account-number standards and institution-specific transfer identifiers; they do not provide a public complete code table or prove that every CBN-listed institution exposes a customer account on NIP.

Sources: https://www.cbn.gov.ng/PaymentsSystem/, https://nibss-plc.com.ng/nigeria-central-switch/, https://nibss-plc.com.ng/nibss-instant-payment/.

## Continued fintech verification

Official Moniepoint support documentation records a `Phone number account` feature but does not publish the underlying digit transformation. Official OPay FAQ documentation states that an OPay user's phone number can be used to make a transaction, but does not establish interbank account-number formatting or leading-zero behavior. Official Kuda FAQ documentation publishes bank code `50211` and sort code `9-50211-15-1`, but does not state that Kuda account numbers are phone numbers.

The resolver now keeps phone-account capabilities in `data/phone-resolution.json` and only marks Moniepoint and OPay as documented phone-capable. Carbon, Kuda, PalmPay, and VFD are not added to phone heuristics because the reviewed public sources do not prove the required behavior. The resolver accepts both 11-digit national input and normalized 10-digit input as caller representations, but does not claim that a provider stores either representation.

Sources:

- https://support.moniepoint.com/topics/account-information-and-updates-234/how-can-i-get-my-account-number-560
- https://static.opayweb.com/faqs
- https://help.kuda.com/en/articles/8954669-faqs


## Account-resolution provider research — 2026-08-25

The user supplied four expected outcomes from integration testing: `2170861119` is UBA, `6666421345` is Moniepoint MFB, `3140537382` is First Bank, and `7085352316` is a phone-based case involving OPay and possibly other providers. The existing checksum resolver includes the first and third institutions among many mathematically compatible candidates; it cannot identify either institution from digits alone. The second and fourth cases are phone-account cases and require provider-specific verification rather than broad phone heuristics.

Official provider documentation was reviewed. Paystack documents a Nigerian `GET /bank/resolve` endpoint that takes `account_number` and `bank_code` and returns account details. Flutterwave documents a Nigerian account-resolution endpoint that takes `account_number` and `account_bank`, returning the resolved account name. Monnify documents a Name Enquiry API that confirms the name tied to an account number using an account number and bank code.

The provider documentation is retained as research context only. The main resolver remains intentionally offline and does not include a provider adapter, network lookup, credential, or name-enquiry dependency. External verification is outside the scope of this self-contained package.


## Account-product variation research — 2026-08-25

Official UBA documentation confirms account opening and funds transfer through UBA's USSD service, including sending to UBA accounts and other banks. It does not publish a universal account-number pattern on the reviewed page. Official Moniepoint documentation confirms separate business and personal banking products and states that a business account number is created instantly after signup; it does not establish that all Moniepoint account numbers follow one single format. Official FirstBank documentation confirms current and savings account products, account opening through branches and digital channels, and that the customer receives an account number after account opening; it does not establish one universal account-number pattern across all products.

Sources:

- https://www.ubagroup.com/nigeria/personal-banking/digital-banking/919-ussd-banking/
- https://moniepoint.com/ng/business/business-account
- https://www.firstbanknigeria.com/personal/accounts/current/current-account/
- https://www.firstbanknigeria.com/open-an-account-from-home-with-firstbank/

These sources verify that the named institutions provide customer accounts and transfer functionality, but they do not justify deriving a bank identity from one sample number or assuming that every product shares one account-number rule.


## Multi-product account research continuation — 2026-08-25

Official UBA documentation confirms that UBA customers can open accounts and transfer funds to UBA accounts, other banks, microfinance banks, fintechs, and other institutions. It does not publish a single universal account-number pattern on the reviewed page.

Official Moniepoint documentation distinguishes personal and business banking products and explicitly states that a business account number is created instantly after signup. This confirms account issuance but does not prove that personal and business account numbers share one pattern.

Official FirstBank documentation confirms current-account products, digital and branch account opening, and notification of the customer’s account number after account opening. It does not prove one universal format across all FirstBank products.

Official OPay FAQ material confirms sending and receiving money through OPay and distinguishes OPay account numbers from recipient bank account numbers, but the reviewed page does not publish a general OPay account-number pattern.

Official PalmPay terms define a PalmPay virtual account number as a personal ten-digit number linked to a PalmPay account for its Pay With Transfer service and state that it can receive payments and accept transfers. This is product-specific evidence, not proof that every PalmPay account or phone number uses that format.

Sources:

- https://www.ubagroup.com/nigeria/personal-banking/digital-banking/919-ussd-banking/
- https://moniepoint.com/ng/business/business-account
- https://www.firstbanknigeria.com/personal/accounts/current/current-account/
- https://www.firstbanknigeria.com/open-an-account-from-home-with-firstbank/
- https://static.opayweb.com/faqs
- https://www.palmpay.com/legal/termsAndConditions/

No account-number rule was generalized from the user’s five sample numbers. Product-specific rules must remain separate from institution-wide rules unless an official source explicitly states that they apply universally.


## Official product-specific account formats — 2026-08-25

Moniepoint's official personal-banking page explicitly says users can use their phone number as their account number. Its official business-account page separately states that a business account number is created instantly. This proves multiple Moniepoint account products and means one sample cannot define all Moniepoint formats.

OPay's official FAQ confirms OPay users can send and receive money and that OPay account details are distinct from recipient bank account details, but the reviewed page does not publish a universal customer account-number format.

PalmPay's official terms explicitly define a personal ten-digit Virtual Account Number linked to the PalmPay account for its Pay With Transfer service and state that it can receive payments and accept transfers. This is a product-specific ten-digit account rule and must not be generalized to every PalmPay product or phone number.

Sources:

- https://moniepoint.com/ng/digital-banking
- https://moniepoint.com/ng/business/business-account
- https://static.opayweb.com/faqs
- https://www.palmpay.com/legal/termsAndConditions/

The resolver data model must represent product-specific formats and phone-account rules separately. It must not use a single institution-wide prefix or rule unless an authoritative source explicitly establishes that scope.
