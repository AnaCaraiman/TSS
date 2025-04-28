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

## 1. Configurare

- Laravel: RefreshDatabase Trait
- Mockery: simulare Repository-uri
- Teste HTTP: Feature testing pentru API
- PHPUnit config cu coverage activ


## 2. Analiza funcțională (Partitionare de echivalență)

| Test | Domeniu | Clase de echivalență |
| :--- | :--- | :--- |
| RegisterUserTest | email, password, phone | valid/invalid email, parola potrivită, prefix corect |
| LoginUserTest | email, password | email existent/inexistent, parola corectă/greșită |
| UpdateCartTest | operation, quantity | valid (+/-/remove), invalid |

## 3. Analiza valorilor de frontieră (Boundary Value Analysis)

| Parametru | Limite |
| :--- | :--- |
| Password | 7 caractere (invalid), 8 caractere (valid) |
| Quantity | 0 (invalid), 1 (valid) |
| Email | format valid vs invalid |

## 4. Testarea Structurală

### a) Acoperire la nivel de instrucțiune
- Trecere prin toate metodele: `registerUser`, `getTokens`, `createCart`, `addCartItem`
- Teste pentru fluxuri de succes și eșec

### b) Acoperire la nivel de decizie
- Testarea tuturor ramurilor din metodele serviciilor
- Verificare coduri HTTP: 200, 201, 400, 401

### c) Acoperire la nivel de condiție
- Validare egalitate parole
- Validare operare cart ("+", "-", "remove")

### d) Testarea circuitelor independente

Calcul McCabe:
- e = 18, n = 16, p = 1
- V(G) = 18 - 16 + 2 = **4** fluxuri independente

Circuite testate:
- Flux de înregistrare corectă
- Flux de login eșuat
- Flux de adăugare produs în coș
- Flux de fallback la operare invalidă


## 5. Rezultate Coverage

| Tip coverage | Status |
| :--- | :--- |
| Statement coverage | ✅ |
| Branch coverage | ✅ |
| Condition coverage | parțial |
| Path coverage | ✅ |


# Concluzie

Testele PHP implementate validează în mod riguros atât comportamentul funcțional, cât și structura internă a serviciilor dezvoltate.

Prin aplicarea metodelor de partitionare de echivalență, analiza valorilor de frontieră și analiza ciclomatică a drumurilor independente, s-a realizat o acoperire completă a cerințelor funcționale și structurale ale aplicației.
