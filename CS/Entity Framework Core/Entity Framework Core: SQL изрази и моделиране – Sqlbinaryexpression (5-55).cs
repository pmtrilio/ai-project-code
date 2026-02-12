
/// <summary>
///     <para>
///         An expression that represents a binary operation in a SQL tree.
///     </para>
///     <para>
///         This type is typically used by database providers (and other extensions). It is generally
///         not used in application code.
///     </para>
/// </summary>
public class SqlBinaryExpression : SqlExpression
{
    private static ConstructorInfo? _quotingConstructor;

    /// <summary>
    ///     Creates a new instance of the <see cref="SqlBinaryExpression" /> class.
    /// </summary>
    /// <param name="operatorType">The operator to apply.</param>
    /// <param name="left">An expression which is left operand.</param>
    /// <param name="right">An expression which is right operand.</param>
    /// <param name="type">The <see cref="Type" /> of the expression.</param>
    /// <param name="typeMapping">The <see cref="RelationalTypeMapping" /> associated with the expression.</param>
    public SqlBinaryExpression(
        ExpressionType operatorType,
        SqlExpression left,
        SqlExpression right,
        Type type,
        RelationalTypeMapping? typeMapping)
        : base(type, typeMapping)
    {
        if (!IsValidOperator(operatorType))
        {
            throw new InvalidOperationException(
                RelationalStrings.UnsupportedOperatorForSqlExpression(
                    operatorType, typeof(SqlBinaryExpression).ShortDisplayName()));
        }

        OperatorType = operatorType;
        Left = left;
        Right = right;
    }

    /// <summary>
    ///     The operator of this SQL binary operation.
    /// </summary>
    public virtual ExpressionType OperatorType { get; }

    /// <summary>
    ///     The left operand.
    /// </summary>
    public virtual SqlExpression Left { get; }