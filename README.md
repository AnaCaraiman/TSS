# TSS – Tema T5: Testare unitară în PHP

## Contextul și importanța testării unitare

Testarea unitară în PHP este o practică esențială în dezvoltarea modernă de aplicații web. Ea presupune scrierea de teste automate pentru metodele și clasele individuale, asigurând funcționalitatea lor corectă.

Framework-ul cel mai folosit este **PHPUnit**, un instrument puternic, stabil și bine integrat în ecosistemul PHP.

---

## Studii și articole de referință

1. [Automated Testing Using PHPUnit](https://www.phparch.com/2023/03/automated-testing-using-phpunit/)
2. [Software Testing Techniques: A Literature Review](https://www.researchgate.net/publication/312484469)
3. [Introducing Automated Unit Testing into Open Source Projects](https://link.springer.com/content/pdf/10.1007/978-3-642-13244-5_32)
4. [Comparative Evaluation of Automated Unit Testing Tool for PHP](https://www.researchgate.net/publication/313208886)
5. [The Relation of Test-Related Factors to Software Quality: A Case Study](https://link.springer.com/article/10.1007/s10664-020-09891-y)
6. [Empirical Study of Test Case and Test Framework Presence in Public Projects on GitHub](https://www.mdpi.com/2076-3417/11/16/7250)

---

## Ghiduri și resurse online utile

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [PHPUnit & GitHub Actions](https://phpunit.readthedocs.io/en/latest/continuous-integration/github-actions.html)
- [Symfony Testing Guide](https://symfony.com/doc/current/testing.html)
- [SitePoint - Unit Testing in PHP with PHPUnit](https://www.sitepoint.com/unit-testing-in-php-with-phpunit/)

---

## Termeni esențiali în testarea unitară

- **Unit test** – test care validează o funcționalitate atomică (ex: o metodă).
- **Assertion** – expresie care verifică dacă un rezultat corespunde celui așteptat (`assertEquals`, `assertTrue`, etc.).
- **Mocking** – simularea unui obiect sau serviciu pentru a testa o unitate în izolare.
- **Test coverage** – măsura în care codul este acoperit de teste.

---

## Comparație între instrumente de testare PHP

| Instrument   | Avantaje                                                             | Dezavantaje                          |
|--------------|----------------------------------------------------------------------|--------------------------------------|
| **PHPUnit**     | Popular, matur, bine documentat, suport CI/CD                    | Necesită configurare inițială XML    |
| **PestPHP**     | Sintaxă modernă și concisă, bazat pe PHPUnit                      | Comunitate mai mică                  |
| **Codeception** | Suportă teste unitare, funcționale și acceptanță                 | Configurare mai complexă             |

---

## Tehnologii complementare și bune practici

- **PHPUnit** – framework-ul principal pentru testare unitară în PHP  
- **GitHub Actions** – pentru rularea automată a testelor în pipeline  
- **Composer** – pentru gestionarea dependențelor  
- **Docker** *(opțional)* – medii izolate de testare  
- **PHPStorm / VSCode** – IDE-uri cu suport pentru integrare testare
