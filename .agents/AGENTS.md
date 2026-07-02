# Reglas del Proyecto (HelpDesk SaaS)

Estas reglas deben respetarse obligatoriamente durante el desarrollo de todas las fases:

1. **Prioridad Arquitectónica:** Antes de implementar cualquier fase, evalúa si el cambio impactará negativamente en la arquitectura objetivo. Si una implementación rápida compromete la escalabilidad, seguridad o mantenibilidad, propón una alternativa y detén la ejecución hasta recibir aprobación. No sacrifiques la arquitectura por velocidad de desarrollo.
2. **Diseño Multi-tenant y Escalable:** Toda funcionalidad debe diseñarse pensando en escalabilidad, reutilización y configuración por cliente.
3. **Cero Implementaciones Rígidas:** Se evitarán implementaciones rígidas o específicas para una sola organización. Siempre que sea posible, las reglas de negocio deberán ser configurables mediante parámetros o paneles administrativos en lugar de código.
