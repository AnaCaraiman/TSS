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

## Scenarii de testare – aplicația PHP

În cadrul aplicației PHP am realizat testare funcțională și structurală pe toate funcționalitățile implementate: coș, catalog, favorite, comenzi și autentificare.  Accent pe:

- testare la nivel de servicii interne (unitară),
- testare prin interfață API (funcțională),
- acoperire structurală (statement, branch și condition coverage),
- validare a fluxurilor reale și a cazurilor de eroare.

### Exemplu detaliat: autentificare

Pentru autentificare am detaliat testele atât la nivel funcțional, cât și structural. Acestea acoperă:

- login reușit vs. eșuat,
- înregistrare cu date valide vs. invalide,
- ștergere cont, logout și generare de tokenuri,
- testarea efectivă a metodelor din `AuthService`, `AuthController` și `TokenRepository`.

#### Exemplu de test funcțional: login reușit

```php
public function test_user_can_login_successfully()
{
    $user = User::factory()->create([
        'password' => bcrypt('secret123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'secret123',
    ]);

    $this->assertTrue(
        in_array($response->status(), [200, 201]),
        'Response status is not 200 or 201'
    );

    $response->assertJsonStructure([
        'message',
        'user',
        'accessToken',
    ]);
}
```



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

| Test                             | Input                                   | Așteptare                                  |
|:----------------------------------|:-----------------------------------------|:--------------------------------------------|
| Înregistrare cu parolă scurtă     | Password = `"abcdefg"` (7 caractere)     | Eșec la înregistrare (HTTP 400)             |
| Înregistrare cu parolă validă     | Password = `"abcdefgh"` (8 caractere)    | Succes înregistrare (HTTP 201)              |
| Înregistrare cu email invalid     | Email = `"exemplu.com"` (fără `@`)       | Eșec înregistrare (HTTP 400)                |
| Adăugare în coș cu cantitate zero | Quantity = `0`                           | Eroare sau fallback la ștergere (HTTP 400)  |


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

Pașii principali ai fluxului de autentificare, înregistrare și operațiuni pe coș

![numbered_control_flow](https://github.com/user-attachments/assets/93cd54f4-dbfc-4de8-b373-11049106b4e9)

## Legenda Pasi

| Pas | Descriere                                            |
|-----|------------------------------------------------------|
| 1   | Start Login                                          |
| 2   | Verificare email                                     |
| 3   | Eroare: „Email invalid”                              |
| 4   | Verificare parola                                    |
| 5   | Eroare: „Wrong email or password”                    |
| 6   | Login reusit                                         |
| 7   | Start Inregistrare                                   |
| 8   | Validare format date intrare                         |
| 9   | Eroare: „Invalid input”                              |
| 10  | Verificare email deja folosit                        |
| 11  | Eroare: „User already exists”                        |
| 12  | Creare utilizator & returnare token si user          |
| 13  | Start operatiune cos                                 |
| 14  | Determinare tip operatiune                           |
| 15  | Adaugare produs (+)                                  |
| 16  | Scădere cantitate (–)                                |
| 17  | Indepartare articol (other)                          |
| 18  | Returnare cos actualizat                             |
| 19  | End                                                  |


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

### Login

| **Nr.** | **C1: Email găsit** | **C2: Parolă corectă** | **Acțiune**                                                      | **Test**                                 | **Descriere**                                           |
|:-------:|:-------------------:|:----------------------:|------------------------------------------------------------------|-------------------------------------------|---------------------------------------------------------|
| **R1**  | Da                  | Da                     | Login reușit → returnează `message`, `user`, `accessToken`       | `test_user_can_login_successfully`        | Verifică login-ul cu email și parolă corecte.           |
| **R2**  | Da                  | Nu                     | Eroare 401 → `"Wrong email or password."`                         | `test_login_fails_with_wrong_credentials` | Verifică că login-ul eșuează când parola este greșită.  |

---

### Register

| **Nr.** | **R1: Email valid (format)** | **R2: User ∉ DB** | **Acțiune**                                                       | **Test**                                    | **Descriere**                                                                                   |
|:-------:|:-----------------------------:|:-----------------:|-------------------------------------------------------------------|----------------------------------------------|-------------------------------------------------------------------------------------------------|
| **R4**  | Nu                            | —                 | Eroare 400 → validare JSON cu `errors`, `message`                 | `test_registration_fails_with_invalid_data`  | Verifică eroarea când email-ul nu are format valid.                                            |
| **R6**  | Da                            | Nu                | Creare user nou → returnează `message`, `user`, `accessToken`      | `test_user_can_register_with_valid_data`     | Înregistrează un utilizator nou cu date valide și confirmă returnarea token-ului și actualizarea DB. |

---

### Cart

| **Nr.** | **Operațiune** | **Acțiune**                                                | **Test**                                      | **Descriere**                                                    |
|:-------:|:--------------:|------------------------------------------------------------|-----------------------------------------------|------------------------------------------------------------------|
| **R7**  | `+`            | Adaugă produs în coș → returnează `items`                  | `test_user_can_add_product_to_cart`           | Testează adăugarea unui produs valid în coș.                      |
| **R8**  | `-`            | Scade cantitatea produsului → returnează `items`           | `test_update_cart_remove_quantity`            | Testează scăderea cantității unui produs existent în coș.        |
| **R9**  | *other*        | Elimină produs din coș → returnează `items`                | `test_invalid_operation_defaults_to_remove_item` | Verifică ștergerea produsului când operațiunea nu este validă. |

---

# 4. Rezultate Coverage

| Tip coverage | Status |
| :--- | :--- |
| Statement coverage | ✅ |
| Branch coverage | ✅ |
| Condition coverage | parțial |
| Path coverage | ✅ |


# 5. Raport coverage (CI)

![WhatsApp Image 2025-05-14 at 21 55 39](https://github.com/user-attachments/assets/b6072795-538a-471a-96a6-38f5621fe523)
Raportul de acoperire generat automat în pipeline arată că testele sunt concentrate pe componentele de tip Service, acolo unde se află logica principală a aplicației.Modelul de testare aplicat este unul eficient și concentrat pe validarea logicii de business. Acoperirea aproape completă a serviciilor confirmă calitatea testării pentru componentele esențiale ale aplicației.



# 6. Mutation Testing (Infection)

Pentru a evalua calitatea reală a testelor unitare și pentru a detecta cazuri în care testele nu reușesc să surprindă modificări semnificative în cod, am integrat în pipeline-ul CI testarea de tip **Mutation Testing** folosind tool-ul [Infection](https://infection.github.io/), specific pentru PHP.

Mutation testing presupune modificarea deliberată (injectarea de mutanți) în codul sursă — de exemplu, schimbarea unui operator `==` în `!=` sau eliminarea unui bloc `if`. Dacă testele nu detectează aceste modificări și nu eșuează, înseamnă că acoperirea nu este semnificativă.

În cazul aplicației noastre, dimensiunea ridicată a codului și testarea extinsă pentru toate microserviciile duc la un număr mare de mutanți generați, mulți dintre aceștia fiind **false positives** sau dependenți de context (de exemplu, acțiuni asupra bazelor de date mockate sau operațiuni async).

Mutation testing a fost integrat în pipeline-ul GitHub Actions, împreună cu analiza de acoperire a codului, și este rulat automat la fiecare `push` în ramura principală. Din considerente de scalabilitate, raportarea completă a mutanților nu este afișată, dar este disponibilă un **summary agregat**, care confirmă rularea corectă a procesului.

> ⚙️ Tool: [Infection PHP](https://infection.github.io/)  
> 🧪 Status: Activ în CI/CD  
> 📈 Rezultat:
![WhatsApp Image 2025-05-16 at 00 15 52_7e2890d4](https://github.com/user-attachments/assets/fd3dea3d-dc7f-47df-b9ac-c987c978982d)



# 7. Evaluarea unei platforme existente

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



