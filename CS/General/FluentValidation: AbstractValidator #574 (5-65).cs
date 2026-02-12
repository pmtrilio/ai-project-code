// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
// http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.
//
// The latest version of this file can be found at https://github.com/FluentValidation/FluentValidation
#endregion

// ReSharper disable MemberCanBePrivate.Global
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