namespace FluentValidation;

using System;
using System.Collections;
using System.Collections.Generic;
using System.Linq;
using System.Linq.Expressions;
using System.Threading;
using System.Threading.Tasks;
using Internal;
using Results;

/// <summary>
/// Base class for object validators.
/// </summary>
/// <typeparam name="T">The type of the object being validated</typeparam>
public abstract partial class AbstractValidator<T> : IValidator<T>, IEnumerable<IValidationRule> {
	internal TrackingCollection<IValidationRuleInternal<T>> Rules { get; } = new();
	private Func<CascadeMode> _classLevelCascadeMode = () => ValidatorOptions.Global.DefaultClassLevelCascadeMode;
	private Func<CascadeMode> _ruleLevelCascadeMode = () => ValidatorOptions.Global.DefaultRuleLevelCascadeMode;

	/// <summary>
	/// <para>
	/// Sets the cascade behaviour <i>in between</i> rules in this validator.
	/// This overrides the default value set in <see cref="ValidatorConfiguration.DefaultClassLevelCascadeMode"/>.
	/// </para>
	/// <para>
	/// If set to <see cref="FluentValidation.CascadeMode.Continue"/> then all rules in the class will execute regardless of failures.
	/// </para>
	/// <para>
	/// If set to <see cref="FluentValidation.CascadeMode.Stop"/> then execution of the validator will stop after any rule fails.
	/// </para>
	/// <para>
	/// Note that cascade behaviour <i>within</i> individual rules is controlled by
	/// <see cref="AbstractValidator{T}.RuleLevelCascadeMode"/>.
	/// </para>
	/// </summary>
	public CascadeMode ClassLevelCascadeMode {
		get => _classLevelCascadeMode();
		set => _classLevelCascadeMode = () => value;
	}

	/// <summary>
	/// <para>
	/// Sets the default cascade behaviour <i>within</i> each rule in this validator.
	/// </para>
	/// <para>
	/// This overrides the default value set in <see cref="ValidatorConfiguration.DefaultRuleLevelCascadeMode"/>.
	/// </para>
	/// <para>
	/// It can be further overridden for specific rules by calling
	/// <see cref="DefaultValidatorOptions.Cascade{T, TProperty}(IRuleBuilderInitial{T, TProperty}, FluentValidation.CascadeMode)"/>.
	/// <seealso cref="RuleBase{T, TProperty, TValue}.CascadeMode"/>.
	/// </para>
	/// <para>
	/// Note that cascade behaviour <i>between</i> rules is controlled by <see cref="AbstractValidator{T}.ClassLevelCascadeMode"/>.
	/// </para>
	/// </summary>
	public CascadeMode RuleLevelCascadeMode {
		get => _ruleLevelCascadeMode();
		set => _ruleLevelCascadeMode = () => value;
	}

	ValidationResult IValidator.Validate(IValidationContext context) {
		ArgumentNullException.ThrowIfNull(context);
		return Validate(ValidationContext<T>.GetFromNonGenericContext(context));
	}

	Task<ValidationResult> IValidator.ValidateAsync(IValidationContext context, CancellationToken cancellation) {
		ArgumentNullException.ThrowIfNull(context);
		return ValidateAsync(ValidationContext<T>.GetFromNonGenericContext(context), cancellation);
	}

	/// <summary>
	/// Validates the specified instance
	/// </summary>
	/// <param name="instance">The object to validate</param>
	/// <returns>A ValidationResult object containing any validation failures</returns>
	public ValidationResult Validate(T instance)
		=> Validate(new ValidationContext<T>(instance, null, ValidatorOptions.Global.ValidatorSelectors.DefaultValidatorSelectorFactory()));
