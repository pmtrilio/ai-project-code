from celery.utils.functional import _regen
from celery.utils.functional import chunks as _chunks
from celery.utils.functional import is_list, maybe_list, regen, seq_concat_item, seq_concat_seq
from celery.utils.objects import getitem_property
from celery.utils.text import remove_repeating_from_task, truncate

__all__ = (
    'Signature', 'chain', 'xmap', 'xstarmap', 'chunks',
    'group', 'chord', 'signature', 'maybe_signature',
)


def maybe_unroll_group(group):
    """Unroll group with only one member.
    This allows treating a group of a single task as if it
    was a single task without pre-knowledge."""
    # Issue #1656
    try:
        size = len(group.tasks)
    except TypeError:
        try:
            size = group.tasks.__length_hint__()
        except (AttributeError, TypeError):
            return group
        else:
            return list(group.tasks)[0] if size == 1 else group
    else:
        return group.tasks[0] if size == 1 else group


def task_name_from(task):
    return getattr(task, 'name', task)


def _stamp_regen_task(task, visitor, append_stamps, **headers):
    """When stamping a sequence of tasks created by a generator,
    we use this function to stamp each task in the generator
    without exhausting it."""

    task.stamp(visitor, append_stamps, **headers)
    return task


def _merge_dictionaries(d1, d2, aggregate_duplicates=True):
    """Merge two dictionaries recursively into the first one.

    Example:
    >>> d1 = {'dict': {'a': 1}, 'list': [1, 2], 'tuple': (1, 2)}
    >>> d2 = {'dict': {'b': 2}, 'list': [3, 4], 'set': {'a', 'b'}}
    >>> _merge_dictionaries(d1, d2)

    d1 will be modified to: {
        'dict': {'a': 1, 'b': 2},
        'list': [1, 2, 3, 4],
        'tuple': (1, 2),
        'set': {'a', 'b'}
    }

    Arguments:
        d1 (dict): Dictionary to merge into.
        d2 (dict): Dictionary to merge from.