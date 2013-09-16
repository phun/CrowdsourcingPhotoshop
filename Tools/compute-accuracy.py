print(__doc__)
import sys
import json
import math
from pprint import pprint

##############################################################################
# Read Turker data file
def read_data(filename):
    try:
        json_data=open(filename)
        data = json.load(json_data)
        # pprint(data)
        json_data.close()
    except IOError:
        "file not found"
        data = []
    return data

##############################################################################
# Parse labels
def parse_labels(workerid, seq, str):
    labels = []
    #print str
    split_str = str.split('\t')
    #print split_str
    for v in split_str:
        if v == '': continue
        split_v = v.split('@')
        label = {}
        label["workerid"] = workerid
        label["time"] = (seq-1)*54 + float(split_v[0])
        label["desc"] = split_v[1]
        labels.append(label)
    return labels


# Compute precision
def compute_precision(tp, fp):
    return (round(float(tp) / (tp + fp), 4))


# Compute recall
def compute_recall(tp, fn):
    return (round(float(tp) / (tp + fn), 4))


# Compute F-Score
def compute_f_score(precision, recall, beta=1):
    if precision == 0 or recall == 0:
        return 0.0000
    score = (1.0 + beta * beta) * (precision * recall) / (beta * beta * precision + recall)
    return round(score, 4)





'''
    Parameters
    turk_filename: without the epsilon part (e.g., s1_p.data)
    truth_filename (e.g., s1_p.truth.json)
'''
# how far away can the labels in turk and truth data be, to be considered correct?
window_size = int(sys.argv[3])

for i in range(1, 2):
    eps = i * 0.07
    global_match = 0
    global_fp = 0
    global_fn = 0
    global_turk_labels = 0
    global_true_labels = 0


    turk_filename = sys.argv[1] + '.' + str(eps) + '.final.json'
    truth_filename = sys.argv[2]
    # read and parse data
    turk = read_data(turk_filename)
    truth = read_data(truth_filename)

    if turk == [] or truth == []:
        continue
    # for each turk input, compare against ground truth and compute measures

    vidlist = turk.keys()
    vidlist.sort()

    for vid in vidlist:
        false_positive = 0
        false_negative = 0
        match = 0
        valid_turk_count = 0

        if vid not in truth:
            print "no ground truth available"
            continue

       # create lid list sorted by time
        lidlist = []
        for lid in turk[vid]:
            # 1) ignore_singletons
            if len(turk[vid][lid]["points_turk"]) < 2:
                # print "singleton skipped"
                continue
            # 2) ignore_unclustered
            # if label["cluster_id"].startswith("-1.0"):
            #     continue
            # 3) ignore_singleton_unclustered
            # if label["cluster_id"].startswith("-1.0") and len(label["points_turk"]) < 2:
            #     continue   
            lidlist.append({'lid': lid, 'time': turk[vid][lid]["time"]})         
        lidlist = sorted(lidlist, key=lambda k: k["time"])
        turk_sorted_lidlist = [item["lid"] for item in lidlist]

        lidlist = []
        for lid in truth[vid]:
            lidlist.append({'lid': lid, 'time': truth[vid][lid]["time"]})
        lidlist = sorted(lidlist, key=lambda k: k["time"])
        truth_sorted_lidlist = [item["lid"] for item in lidlist]

        # get pairwise cost matrix between turk and truth
        import matching
        cost_matrix = matching.get_cost_matrix(vid, turk, truth, turk_sorted_lidlist, truth_sorted_lidlist, window_size)
        matches = matching.maxWeightMatching(cost_matrix)
        print matches
        turk_to_truth = matches[0]
        truth_to_turk = matches[1]

        if True:
            for index in turk_to_truth:
                found = 0
                # print index, turk_to_truth[index]
                # non-existing entries added to Turk labels, skip
                if len(turk_sorted_lidlist) <= index:
                    # print "[fn]"
                    continue
                else:
                    valid_turk_count += 1
                    turk_lid = turk_sorted_lidlist[index]
                    turk_label = turk[vid][turk_lid]
                    if len(truth_sorted_lidlist) <= turk_to_truth[index]:
                        pass
                        # print "[fp]", turk_label["time"]
                        # false_positive += 1
                        # for t in turk_label["points_turk"]: 
                        #     print "    Turk:", t["time"], t["desc"].encode("utf-8")
                    else:
                        true_lid = truth_sorted_lidlist[turk_to_truth[index]]
                        true_label = truth[vid][true_lid]
                        distance = math.fabs(float(turk_label["time"]) - float(true_label["time"]))
                        if found is not 1 and "matched_new" not in true_label and distance <= window_size:
                            
                            true_label["matched_new"] = True
                            vals = []
                            print "[m ]", turk_label["time"], true_label["time"], "[", distance, "]", true_label["desc"]
                            for t in turk_label["points_turk"]: 
                                vals.append(t["time"])
                                print "    Turk:", t["time"], t["desc"].encode("utf-8")
                            radius = max(vals) - min(vals)
                            print "  Radius", radius
                            if radius <= 20:
                                found += 1
                if found == 1:
                    match += 1
                elif found > 1:
                    print "not possible"                    
                elif found == 0:
                    print "[fp]", turk_label["time"]
                    
                    vals = []
                    for t in turk_label["points_turk"]: 
                        vals.append(t["time"])
                        print "    Turk:", t["time"], t["desc"].encode("utf-8")
                    radius = max(vals) - min(vals)
                    if radius <= 20:
                        false_positive += 1
            for true_id in truth_sorted_lidlist:
                if "matched_new" not in truth[vid][true_id]:
                    false_negative += 1
                    print "[fn]", truth[vid][true_id]["time"], truth[vid][true_id]["desc"]


        if False:
            print "==="
            # for lid in turk[vid]:
            for lid in turk_sorted_lidlist:
                label = turk[vid][lid]
                found = 0
                valid_turk_count += 1

                # Find a closest match between ground truth and turker input
                # candidates = []
                # distances = []
                # for true_id in truth[vid]:
                for true_id in truth_sorted_lidlist:
                    # print truth[vid]
                    true_label = truth[vid][true_id]
                    #print math.fabs(float(label["time"]) - float(true_label["time"]))
                    distance = math.fabs(float(label["time"]) - float(true_label["time"]))
                    if found is not 1 and "matched" not in true_label and distance <= window_size:
                        found += 1
                        true_label["matched"] = True
                        print "[m ]", label["time"], true_label["time"], true_label["desc"]
                        # for turk_label in label["points_turk"]: 
                        #     print "Turk:", turk_label["time"], turk_label["desc"].encode("utf-8")

                #         candidates.append(true_label)
                #         distances.append(distance)
                # if len(distances) > 0:
                #     import operator
                #     matching_index, matching_value = min(enumerate(distances), key=operator.itemgetter(1))
                #     matching_label = candidates[matching_index]

                if found == 1:
                    match += 1
                elif found > 1:
                    print "not possible"
                elif found == 0:   # no match found for this label in the truth
                    false_positive += 1
                    print "[fp]", label["time"]
                    # for turk_label in label["points_turk"]: 
                    #     print "Turk:", turk_label["time"], turk_label["desc"].encode("utf-8")

            for true_id in truth[vid]:
                if "matched" not in truth[vid][true_id]:
                    false_negative += 1
                    print "[fn]", truth[vid][true_id]["time"], truth[vid][true_id]["desc"]


        if match + false_positive == 0 or match + false_negative == 0:
            print "no matches for this video."
        else:    
            # print vid, "#Turk:", len(turk[vid]), " #True:", len(truth[vid]), " #Match:", match, " #FP:", false_positive, " #FN:", false_negative
            precision = compute_precision(match, false_positive)
            recall = compute_recall(match, false_negative)
            f1 = compute_f_score(precision, recall, 1)
            f2 = compute_f_score(precision, recall, 2)
            print "[%s]  Precision: %.4f  Recall:%.4f  F1:%.4f  F2:%.4f" % (vid, precision, recall, f1, f2), "turk:", valid_turk_count, "true:", len(truth[vid]), "match:", match, "fp:", false_positive, "fn:", false_negative
        global_match += match
        global_fp += false_positive
        global_fn += false_negative
        global_turk_labels += len(turk[vid])
        global_true_labels += len(truth[vid])

    # if (global_turk_labels == 0 or global_true_labels == 0):
    #     print "eps:", eps, "0 labels"
    # else:
    # print "eps:", eps, "GLOBAL Precision:", float(global_match) / global_turk_labels, " GLOBAL Recall:", float(global_match) / global_true_labels
    print "====================================="
    global_precision = compute_precision(global_match, global_fp)
    global_recall = compute_recall(global_match, global_fn)
    global_f1 = compute_f_score(global_precision, global_recall, 1)
    global_f2 = compute_f_score(global_precision, global_recall, 2)
    print "eps:", eps, "GLOBAL Precision: %.4f Recall:%.4f  F1:%.4f  F2:%.4f" % (global_precision, global_recall, global_f1, global_f2)

# final_data = {}
# for vid in vidlist:
#     print "\n" 
#     sorted_turk = sorted(turk[vid], key=lambda tup: tup["time"])

#     # if (vid == "s1_p03_v03"):
#     #     print "ORG:"
#     #     for label in turk[vid]:
#     #         print label["time"], label["desc"]
#     #     print "SORTED:"
#     #     for label in sorted_turk:
#     #         print label["time"], label["desc"]

#     print vid, "Turk:", len(turk[vid]), "Truth:", len(truth[vid])#, data[vid]
#     # only grab time information from the data
#     label_turk_list = []
#     label_truth_list = []
#     for label in turk[vid]:
#         label_turk_list.append(label["time"])
#     for label in truth[vid]:
#         label_truth_list.append(label["time"])
#     label_turk_list.sort()
#     label_truth_list.sort()
#     # format for DBSCAN
    
#     labels_turk = format_data(label_turk_list)
#     #labels_truth = label_truth_list #format_data(label_truth_list)
#     # now run DBSCAN
#     clusters = run_dbscan(labels_turk, label_turk_list, label_truth_list)


#     # if (vid == "s1_p03_v03"):
#     print "TRUE:"
#     for label in truth[vid]:
#         print label["time"], label["desc"]
#     print "==="
#     print "TURK:"
#     cluster_data = {}
#     for (i, label) in enumerate(label_turk_list):
#         key = str(clusters[i])
#         entry = {'cluster_id': key, 'time': label, 'workerid': sorted_turk[i]["workerid"], 'desc': sorted_turk[i]["desc"]}
#         if key in cluster_data:
#             cluster_data[key].append(entry)
#         else:
#             cluster_data[key] = []
#         #print cluster_data[key]
#         print label, "(", int(clusters[i]), ")", sorted_turk[i]["workerid"], sorted_turk[i]["desc"]

#     final_data[vid] = {}
#     for cid in cluster_data:
#         #print cid, cluster_data[cid]
#         final_data[vid][cid] = {'cluster_id': cid, 'time': getClusterTime(cluster_data[cid], cid), 'points_turk': cluster_data[cid]}
#         print "---"
#         print final_data[vid][cid]
#     # for cluster in cluster_data:
#     #     final_data[vid] = {'cluster_id': cluster["cluster_id"], 'time': getClusterTime(cluster), 'points_turk': cluster}

#     import json
#     with open(sys.argv[2] + '.final.json', 'wb') as fp:
#         json.dump(final_data, fp)

