# Call_duration_20m
[macro-queue-duration]
exten => s,1,NoOp(${NODEST}  ${CHANNEL})
exten => s,n,System(/var/lib/asterisk/agi-bin/duration.php '${UNIQUEID}' '${CHANNEL}' '${NODEST}' &)
;exten => s,n,NoOp(---${TEST}---)
;exten => s,n,DumpChan(3)
exten => s,n,Return

[spy-duration]
exten => 1,1,Chanspy(${chan},Wqv(4))
exten => 1,n,Hangup

exten => 2,1,Answer
exten => 2,n,Set(VOLUME(TX)=-3)
exten => 2,n,Playback(${audio})
exten => 2,n,Hangup

[spy-end]
exten => 1,1,Chanspy(${chan},qBSv(4))
exten => 1,n,Hangup

exten => 2,1,Answer
exten => 2,n,Set(VOLUME(TX)=-3)
exten => 2,n,Playback(${audio})
exten => 2,n,Hangup

; end of [spy]


free-pbx overide:

[macro-auto-blkvm]
include => macro-auto-blkvm-custom
exten => s,1,System(/var/lib/asterisk/agi-bin/duration.php '${UNIQUEID}' '${CHANNEL}' '${NODEST}' &)

;exten => s,n,DumpChan(3)
exten => s,n,System(asterisk -rx "queue remove member Local/${dynagent}@from-queue/n from 11802157145")
exten => s,n,System(asterisk -rx "queue remove member Local/${dynagent}@from-queue/n from 1180145")

exten => s,n,ExecIf($["${FROMQ}" = "true" & "${CALLFILENAME}" != "" & "${CDR(recordingfile)}" = ""]?Set(CDR(recordingfile)=${CALLFILENAME}.${MON_FMT}))
exten => s,n,Set(__MACRO_RESULT=)
exten => s,n,Set(CFIGNORE=)
exten => s,n,Set(MASTER_CHANNEL(CFIGNORE)=)
exten => s,n,Set(FORWARD_CONTEXT=from-internal)
exten => s,n,Set(MASTER_CHANNEL(FORWARD_CONTEXT)=from-internal)
exten => s,n,Macro(blkvm-clr,)
exten => s,n,ExecIf($[!${REGEX("[^0-9]" ${DIALEDPEERNUMBER})} && "${DB(AMPUSER/${AMPUSER}/cidname)}" != ""]?Set(MASTER_CHANNEL(CONNECTEDLINE(num))=${DIALEDPEERNUMBER}))
exten => s,n,ExecIf($[!${REGEX("[^0-9]" ${DIALEDPEERNUMBER})} && "${DB(AMPUSER/${AMPUSER}/cidname)}" != ""]?Set(MASTER_CHANNEL(CONNECTEDLINE(name))=${DB(AMPUSER/${DIALEDPEERNUMBER}/cidname)}))

;--== end of [macro-auto-blkvm] ==--;
