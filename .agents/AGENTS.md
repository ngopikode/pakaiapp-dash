# Coding Standards

## Service Injection Guidelines
When injecting Services or Dependencies into a Controller or another Service, follow these rules:

1. **Constructor Promotion (Many Usages)**:
   If the injected service is used by ALL or ALMOST ALL methods in the class, use Constructor Property Promotion:
   ```php
   public function __construct(protected readonly TargetService $targetService) {}
   ```

2. **Lazy Initialization (Few Usages)**:
   If the injected service is only used by ONE or A FEW methods (where creating it always would be a waste of resources), use Lazy Initialization with a nullable property and a getter method. DO NOT use `app(TargetService::class)` inline repeatedly.
   ```php
   protected ?TargetService $targetService = null;

   protected function targetService(): TargetService
   {
       return $this->targetService ??= app(TargetService::class);
   }
   ```
