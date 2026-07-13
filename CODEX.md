# Codex Guidelines: CosmicLib Engine

This file instructs Codex/GitHub Copilot on how to complete code inline for this project.

## Key Directives

1. **Inline Documentation**: Always write cleanDoc blocks in standard PHP DocBlock format:
   ```php
   /**
    * Melakukan transaksi peminjaman buku oleh anggota.
    *
    * @param int $memberId
    * @param array $bookIds
    * @return \CosmicLib\Core\Models\BorrowRecord
    * @throws \CosmicLib\Core\Exceptions\CirculationException
    */
   ```

2. **Language Split**:
   - Write UI and user-facing messages in **Indonesian**.
   - Write all code constructs (classes, properties, functions, databases) in **English**.

3. **Service Layer Resolution**:
   - Do not perform heavy queries or logic inside Controllers.
   - Resolve logic from services via dependency injection:
   ```php
   public function store(BorrowRequest $request, CirculationService $circulation)
   {
       $circulation->processBorrow($request->validated());
   }
   ```
