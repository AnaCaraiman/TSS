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

### Structură directoare teste
```
tests/
├── Feature/
│   ├── Auth/RegisterUserTest.php
│   ├── Auth/LoginUserTest.php
│   └── Cart/UpdateCartTest.php
└── Unit/Services/
    ├── AuthServiceTest.php
    ├── CartServiceTest.php
    └── CartItemServiceTest.php
```

## Etapa 2 

## 1. Configurare mediu de testare

- Teste unitare: Mockery pentru simularea depozitelor (repositories)
- Teste funcționale: RefreshDatabase pentru resetarea bazei de date
- Setup automat GitHub Actions pentru rulare la push


## 2. Equivalence Class Analysis (Strict pe teste)

| Test | Input principal | Clase de echivalență |
| :--- | :--- | :--- |
| RegisterUserTest | email, password, phone | email valid/invalid; password matching; prefix valid |
| LoginUserTest | email, password | email existent vs inexistent; parola corectă vs incorectă |
| UpdateCartTest | operation (+, -, remove) | valid add/remove/invalid |


## 3. Boundary Value Analysis

| Parametru | Valori testate |
| :--- | :--- |
| password length | >=8 caractere |
| quantity in cart update | >0 |
| email format | valid vs invalid |


## 4. Independent Path Testing (control flow)

| Test | Fluxuri testate |
| :--- | :--- |
| RegisterUserTest | validare corectă, invalid email |
| LoginUserTest | succes autentificare, parolă greșită |
| UpdateCartTest | operare +/-, fallback operare invalidă |
| CartServiceTest | creare coș success/fail |
| CartItemServiceTest | adăugare produs success/fail, modificare cantitate |


## 5. Rezumat Coverage

| Tip Coverage | Realizat |
| :--- | :--- |
| Statement coverage | ✅ |
| Branch coverage | ✅ |
| Condition coverage | Partial |
| Path coverage | ✅ |


# Concluzie

Testele existente validează atât funcționalitatea pozitivă, cât și gestionarea erorilor sau a fluxurilor alternative.

Prin aplicarea testării funcționale și structurale, împreună cu Mockery și PHPUnit, s-a realizat o acoperire semnificativă a codului aplicației PHP.

Integrarea automata prin GitHub Actions și măsurarea coverage-ului permit o dezvoltare continuă și sigură.
