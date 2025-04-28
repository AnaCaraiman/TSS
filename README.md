# TSS – Tema T5: Testare unitară în PHP
## Etapa 1 

## 1. Introducere

Testarea unitară în PHP este o practică esențială în dezvoltarea modernă de aplicații web. Ea presupune scrierea de teste automate pentru metodele și clasele individuale, asigurând funcționalitatea lor corectă.

Framework-ul cel mai folosit este **PHPUnit**, un instrument puternic, stabil și bine integrat în ecosistemul PHP.


## 2. Definiții esențiale

- **Testare unitară:** verificarea celor mai mici unități testabile (metode, clase) pentru comportament corect.
- **Mocking:** simularea de servicii externe, precum baze de date sau API-uri.
- **Coverage:** proporția de cod sursă acoperită de cazurile de testare.
- **Functional Testing:** verificarea faptului că sistemul îndeplinește cerințele funcționale specificate.
- **Structural Testing:** verificarea internă a fluxurilor de execuție și a ramurilor logice.


## 3. Servicii și resurse disponibile

| Serviciu/Resursă | Descriere |
| :--- | :--- |
| ⚙️ GitHub Actions | Rulează automat testele la fiecare push (CI/CD) |
| 📈 Codecov | Măsoară procentul de cod acoperit de teste (coverage) |
| 🌐 Mockery | Simulează obiecte și metode externe pentru testare izolată |
| 💾 SQLite In-Memory | Testare rapidă, izolată, folosind baze de date în memorie |


## 4. Analiza Framework-urilor de Testare

| Criteriu | PHPUnit | PestPHP | Codeception |
| :--- | :--- | :--- | :--- |
| Popularitate | Foarte mare, standard de industrie | În creștere rapidă | Foarte bun pentru testare integrată |
| Configurare | Medie (phpunit.xml) | Minimală | Complexă |
| Mocking integrat | Da (cu Mockery) | Da (cu pluginuri) | Da |
| Raport Coverage | Cu plugin extern | Cu plugin Pest-Coverage | Integrat |


## 5. Analiza aplicațiilor existente

| Framework | Avantaje | Dezavantaje |
| :--- | :--- | :--- |
| PHPUnit | Stabilitate, suport larg, integrare CI/CD | Necesită configurare XML |
| PestPHP | Sintaxă scurtă, concisă, rapidă | Comunitate mai mică |
| Codeception | Suportă teste unitare, funcționale și de acceptanță | Mai greu de configurat pentru proiecte mici |


## 6. Articole științifice relevante


1. [Automated Testing Using PHPUnit](https://www.phparch.com/2023/03/automated-testing-using-phpunit/)
2. [Introducing Automated Unit Testing into Open Source Projects](https://link.springer.com/content/pdf/10.1007/978-3-642-13244-5_32)
3. [Comparative Evaluation of Automated Unit Testing Tool for PHP](https://www.researchgate.net/publication/313208886)


## 7. Pagini web utile

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Laravel Testing Docs](https://laravel.com/docs/testing)
- [Mockery Documentation](https://docs.mockery.io/en/latest/)


## 8. Setup de bază pentru testare

### Inițializare proiect PHP + Laravel

```bash
composer install
composer require --dev phpunit/phpunit mockery/mockery
```

### Configurare phpunit.xml

```xml
<phpunit bootstrap="vendor/autoload.php" colors="true">
    <testsuites>
        <testsuite name="Application Test Suite">
            <directory>./tests</directory>
        </testsuite>
    </testsuites>
</phpunit>
```


# Etapa 2 

# 1. Configurare

- Laravel: RefreshDatabase Trait
- Mockery: simulare Repository-uri
- Teste HTTP: Feature testing pentru API
- PHPUnit config cu coverage activ


# 2. Testarea funcțională

## (a) Partiționare de echivalență

### Domeniul de intrări:

**RegisterUserTest:**
- `email` → două clase de echivalență:
  - E1: { email valid }
  - E2: { email invalid }
- `password` → două clase:
  - P1: { parolă validă ≥ 8 caractere }
  - P2: { parolă invalidă < 8 caractere }
- `phone_prefix` și `phone_number` → tratate ca:
  - PP1: { prefix valid }
  - PP2: { prefix invalid }

**LoginUserTest:**
- `email`, `password`
  - LE1: { email valid și existent }
  - LE2: { email valid dar parola greșită }

**UpdateCartTest:**
- `operation`
  - OP1: { „+” - adaugă }
  - OP2: { „-” - scade }
  - OP3: { „remove” - șterge }
  - OP4: { operație invalidă – fallback la remove }
- `quantity`
  - Q1: { >0 }
  - Q2: { 0 sau negativ – invalid }

### Domeniul de ieșire:

- HTTP 201 (Created) – succes înregistrare
- HTTP 400 (Bad Request) – date invalide
- HTTP 401 (Unauthorized) – login greșit
- HTTP 200 (OK) – operații coș reușite

### Clase globale de echivalență construite:

| Clasă | Condiții |
|:---|:---|
| G_111 | Email valid, parola validă, prefix valid |
| G_211 | Email invalid, restul valid |
| G_121 | Email valid, parolă invalidă |
| G_112 | Email valid, prefix invalid |
| G_221 | Email invalid, parolă invalidă |

**Exemple de cazuri concrete extrase din teste:**
- G_111: (abcdef@yahoo.com, password123, +40)
- G_211: (not-an-email, password123, +40)
- G_121: (abcdef@yahoo.com, pass, +40)


## (b) Analiza valorilor de frontieră

Analiza aplicată pe:
- Lungimea parolei
- Formatul emailului
- Cantitatea în coș

### Limite relevante:

| Parametru | Valori testate |
|:---|:---|
| Password | 7 caractere → invalid, 8 caractere → valid |
| Email | string valid / string fără `@` sau `.`, invalid |
| Quantity | 0 (invalid) / 1 (valid) |

### Seturi de date:

| Test | Input | Așteptare |
|:---|:---|:---|
| Password = 7 caractere | Eșec la înregistrare (400) |
| Password = 8 caractere | Succes înregistrare (201) |
| Email fără `@` | Eșec înregistrare (400) |
| Quantity = 0 | Fallback / eroare la adăugare în coș |


# 3. Testarea structurală

## (a) Graful de flux de control

Fluxuri verificate explicit în testele unitare și funcționale:
- Înregistrare corectă
- Înregistrare eșuată
- Login corect
- Login eșuat
- Adăugare produs în coș
- Eliminare produs din coș
- Operare invalidă fallback


## (b) Acoperire la nivel de instrucțiune (statement coverage)

**Asigurat de teste:**
- Creare utilizator cu date valide
- Gestionare erori la input invalid
- Generare tokenuri
- Operații createCart / deleteCart / clearCart

✔️ Toate metodele majore sunt parcurse cel puțin o dată.


## (c) Acoperire la nivel de decizie (decision coverage)

| Decizie | Acoperire |
|:---|:---|
| Validarea emailului la register | True + False |
| Validarea parolei | True + False |
| Alegerea operației în UpdateCart | +, -, remove, invalid fallback |

✔️ Toate deciziile principale din flux au fost testate atât pentru ramura pozitivă cât și pentru cea negativă.


## (d) Acoperire la nivel de condiție (condition coverage)

| Condiție | Acoperire |
|:---|:---|
| Email valid vs invalid | True/False |
| Password length corectă vs incorectă | True/False |
| Cantitate pozitivă în coș vs 0 | True/False |


## (e) Testarea circuitelor independente

Folosind complexitatea ciclomatică McCabe:
- e = 18 muchii
- n = 16 noduri
- p = 1 componentă conectată

**V(G) = 18 - 16 + 2×1 = 4**

Testele acoperă 4 drumuri independente:
- Înregistrare succes
- Înregistrare eroare email
- Login succes
- Operare invalidă fallback


## Tabel de Decizie

| **Nr. Regula** | **Regula** | **C1 (Email corect)** | **C2 (Parola corectă)** | **C3 (Înregistrare completă)** | **C4 (User existent)** | **C5 (Produs existent)** | **Acțiune** | **Test** | **Descriere** |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| **R1** | Login reușit | Da | Da | Da | Nu | Nu | Login reușit, returnează token și user | `test_user_can_login_successfully` | Verifică login-ul cu email și parolă corecte, se returnează un **message**, **user** și **accessToken**. |
| **R2** | Eșec login cu credențiale greșite | Da | Nu | Nu | Nu | Nu | Eroare login, mesaj: "Wrong email or password." | `test_login_fails_with_wrong_credentials` | Verifică că login-ul eșuează când parola este greșită, iar mesajul de eroare este **"Wrong email or password."**. |
| **R3** | Înregistrare cu email deja folosit | Da | Da | Da | Da | Nu | Înregistrare cu email deja folosit, mesaj: "User already exists" | `test_registration_fails_with_invalid_data` | Verifică eroarea înregistrării unui utilizator cu un email deja existent. |
| **R4** | Creare user nou | Da | Da | Da | Nu | Nu | Creare user nou, returnează message, user, accessToken | `test_user_can_register_with_valid_data` | Înregistrarea unui utilizator nou cu date valide, returnează un **message**, **user** și **accessToken**. |
| **R5** | Eroare login, mesaje incorecte | Nu | Nu | Nu | Nu | Nu | Eroare, mesaj: "Email invalid" | `test_registration_fails_with_invalid_data` | Verifică eroarea pentru un email invalid, mesajul de eroare fiind **"Email invalid"**. |
| **R6** | Produs inexistent | Nu | Nu | Nu | Nu | Da | Eroare, mesaj: "Produs inexistent" | `test_user_can_add_product_to_cart` | Verifică eroarea în cazul în care produsul nu există. |
| **R7** | Adăugare produs în coș | Da | Da | Da | Nu | Da | Adăugare produs în coș, returnează actualizare cart | `test_user_can_add_product_to_cart` | Adaugă un produs valid în coș, returnează actualizarea coșului. |
| **R8** | Actualizare cantitate în coș | Da | Da | Nu | Nu | Da | Actualizare cantitate în coș, returnează nouă stare coș | `test_update_cart_add_quantity` | Actualizează cantitatea unui produs din coș, returnează coșul actualizat. |
| **R9** | Actualizare cantitate în coș (scădere cantitate) | Da | Da | Nu | Nu | Da | Actualizare cantitate în coș, returnează nouă stare coș | `test_update_cart_remove_quantity` | Scade cantitatea unui produs din coș, returnează coșul actualizat. |
| **R10** | Operațiune invalidă | Da | Da | Nu | Nu | Da | Operațiune invalidă, produs eliminat din coș | `test_invalid_operation_defaults_to_remove_item` | Verifică comportamentul în caz de operațiune invalidă, produsul este eliminat din coș. |

## Graful fluxului de control

![17458436413761939473873875167879](https://github.com/user-attachments/assets/8d16f8c6-33b4-4b58-a46b-1afc574e3106)


# 4. Rezultate Coverage

| Tip coverage | Status |
| :--- | :--- |
| Statement coverage | ✅ |
| Branch coverage | ✅ |
| Condition coverage | parțial |
| Path coverage | ✅ |



# Concluzie

Testele PHP implementate validează în mod riguros atât comportamentul funcțional, cât și structura internă a serviciilor dezvoltate.

Prin aplicarea metodelor de partitionare de echivalență, analiza valorilor de frontieră și analiza ciclomatică a drumurilor independente, s-a realizat o acoperire completă a cerințelor funcționale și structurale ale aplicației.



# 5. Evaluarea unei platforme existente

## Descriere: 
Magento este o platformă open-source de e-commerce lansată în 2008. Este scrisă în PHP și utilizează baze de date MySQL sau MariaDB. Magento oferă o arhitectură modulară și suportă extensii și personalizări variate.

## Avantaje:
- Platformă completă — Tot ce ai nevoie pentru un magazin online este deja integrat: catalog produse, management comenzi, plăți, livrare, rapoarte.
- Multi-store — Poți administra mai multe magazine dintr-un singur panou de control.
- Extensibilitate uriașă — Există mii de module și teme pentru a personaliza aproape orice aspect.

## Dezavantaje:
- Complexitate mare — Curbă de învățare abruptă, necesită dezvoltatori cu experiență.
- Resurse consumatoare — Necesită servere puternice, mai ales pentru magazine mari.
- Costuri ascunse — Deși este open-source, multe extensii utile sunt contra cost.
- Update-uri dificile — Versiunile noi pot necesita refactorizări serioase la module personalizate.

## Comparație Magento vs Proiectul nostru

| Caracteristică         | Magento                                        | Proiectul nostru                                |
|------------------------|------------------------------------------------|-------------------------------------------------|
| **Arhitectura**         | Monolitică, extensibilă prin module           | Microservicii independente                      |
| **Complexitate**        | Ridicată, necesită experiență                 | Medie, ușor de înțeles pentru începători        |
| **Extensibilitate**     | Foarte mare (module, teme, marketplace)       | Redusă, orientată pe învățare                   |
| **Resurse necesare**    | Servere puternice, echipe specializate        | Resurse minime, poate rula local                |



