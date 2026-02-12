using System.Collections.Generic;
using System.Diagnostics;
using System.Diagnostics.CodeAnalysis;
using System.Runtime.CompilerServices;
using System.Runtime.ExceptionServices;
using System.Runtime.Versioning;
using System.Text;
using System.Threading;
using System.Threading.Tasks;
using Microsoft.Win32.SafeHandles;

namespace System.IO
{
    // Class for creating FileStream objects, and some basic file management
    // routines such as Delete, etc.
    public static partial class File
    {
        private const int ChunkSize = 8192;

        // UTF-8 without BOM and with error detection. Same as the default encoding for StreamWriter.
        private static Encoding UTF8NoBOM => field ??= new UTF8Encoding(encoderShouldEmitUTF8Identifier: false, throwOnInvalidBytes: true);

        internal const int DefaultBufferSize = 4096;

        public static StreamReader OpenText(string path)
            => new StreamReader(path);

        public static StreamWriter CreateText(string path)
            => new StreamWriter(path, append: false);

        public static StreamWriter AppendText(string path)
            => new StreamWriter(path, append: true);

        /// <summary>
        /// Copies an existing file to a new file.
        /// An exception is raised if the destination file already exists.
        /// </summary>
        public static void Copy(string sourceFileName, string destFileName)
            => Copy(sourceFileName, destFileName, overwrite: false);

        /// <summary>
        /// Copies an existing file to a new file.
        /// If <paramref name="overwrite"/> is false, an exception will be
        /// raised if the destination exists. Otherwise it will be overwritten.
        /// </summary>
        public static void Copy(string sourceFileName, string destFileName, bool overwrite)
        {
            ArgumentException.ThrowIfNullOrEmpty(sourceFileName);
            ArgumentException.ThrowIfNullOrEmpty(destFileName);

            FileSystem.CopyFile(Path.GetFullPath(sourceFileName), Path.GetFullPath(destFileName), overwrite);